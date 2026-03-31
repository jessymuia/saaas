#!/bin/bash
set -e

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
