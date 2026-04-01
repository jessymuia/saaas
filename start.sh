#!/bin/bash
set -e

# ── Keep APP_URL in sync with the current Replit dev domain ──────────────────
if [ -n "$REPLIT_DEV_DOMAIN" ]; then
    # In Replit, all traffic goes through the Next.js proxy on port 5000.
    # APP_URL and ASSET_URL must point to port 5000 so that login redirects
    # and generated URLs stay within the proxy instead of jumping to port 8000.
    # MPESA callbacks use port 8000 directly (server-to-server, no proxy needed).
    PROXY_URL="https://${REPLIT_DEV_DOMAIN}:5000"
    BACKEND_URL="https://${REPLIT_DEV_DOMAIN}:8000"
    sed -i "s|APP_URL=.*|APP_URL=${PROXY_URL}|" .env
    grep -q "ASSET_URL=" .env \
        && sed -i "s|ASSET_URL=.*|ASSET_URL=${PROXY_URL}|" .env \
        || echo "ASSET_URL=${PROXY_URL}" >> .env
    sed -i "s|MPESA_CALLBACK_URL=.*|MPESA_CALLBACK_URL=${BACKEND_URL}/api/mpesa/callback|" .env
    sed -i "s|CENTRAL_DOMAINS=.*|CENTRAL_DOMAINS=${REPLIT_DEV_DOMAIN},localhost,127.0.0.1|" .env
fi

# ── Start Redis (required for cache / queue) ──────────────────────────────────
if ! redis-cli ping &>/dev/null 2>&1; then
    echo "Starting Redis..."
    redis-server --daemonize yes --logfile /tmp/redis.log
    sleep 1
fi

# ── Clear caches ──────────────────────────────────────────────────────────────
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# ── Create storage symlink if missing ─────────────────────────────────────────
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# ── Start the queue worker in background ──────────────────────────────────────
php artisan queue:work --daemon --tries=3 &>/tmp/queue.log &

# ── Start the Laravel dev server on port 8000 ────────────────────────────────
echo "Starting Laravel on port 8000..."
php artisan serve --host=0.0.0.0 --port=8000
