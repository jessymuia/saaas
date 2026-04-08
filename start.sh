#!/bin/bash
set -e

# ── Generate .env from environment variables ──────────────────────────────────
cat > .env <<EOF
APP_NAME="${APP_NAME:-PropManage SaaS}"
APP_ENV=${APP_ENV:-local}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-true}
APP_URL=http://localhost:8000

LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=${LOG_LEVEL:-debug}

# Database
DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${PGHOST:-helium}
DB_PORT=${PGPORT:-5432}
DB_DATABASE=${PGDATABASE:-heliumdb}
DB_USERNAME=${PGUSER:-postgres}
DB_PASSWORD=${PGPASSWORD:-}

# Cache / Queue / Session
BROADCAST_DRIVER=${BROADCAST_DRIVER:-log}
CACHE_DRIVER=${CACHE_DRIVER:-file}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}

# Redis
REDIS_CLIENT=${REDIS_CLIENT:-predis}
REDIS_HOST=${REDIS_HOST:-127.0.0.1}
REDIS_PASSWORD=null
REDIS_PORT=${REDIS_PORT:-6379}

# Mail
MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-localhost}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME:-}
MAIL_PASSWORD=${MAIL_PASSWORD:-}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-tls}
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-}"
MAIL_FROM_NAME="${MAIL_FROM_NAME:-PropManage SaaS}"

# Multi-tenancy
CENTRAL_DOMAINS=${CENTRAL_DOMAINS:-localhost,127.0.0.1}
TENANT_DOMAIN_SUFFIX=${TENANT_DOMAIN_SUFFIX:-.yourdomain.com}
APP_TENANT_MODE=${APP_TENANT_MODE:-slug}

# M-Pesa
MPESA_ENV=${MPESA_ENV:-sandbox}
MPESA_CONSUMER_KEY=${MPESA_CONSUMER_KEY:-}
MPESA_CONSUMER_SECRET=${MPESA_CONSUMER_SECRET:-}
MPESA_SHORTCODE=${MPESA_SHORTCODE:-}
MPESA_PASSKEY=${MPESA_PASSKEY:-}
MPESA_CALLBACK_URL=http://localhost:8000/api/mpesa/callback
MPESA_STK_PUSH_URL=${MPESA_STK_PUSH_URL:-https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest}

# Filament
FILAMENT_FILESYSTEM_DISK=${FILAMENT_FILESYSTEM_DISK:-public}

# Vite
VITE_APP_NAME="${APP_NAME:-PropManage SaaS}"
EOF

# ── Keep APP_URL in sync with the current Replit dev domain ──────────────────
if [ -n "$REPLIT_DEV_DOMAIN" ]; then
    PUBLIC_URL="https://${REPLIT_DEV_DOMAIN}:8000"
    sed -i "s|APP_URL=.*|APP_URL=${PUBLIC_URL}|" .env
    sed -i "s|MPESA_CALLBACK_URL=.*|MPESA_CALLBACK_URL=${PUBLIC_URL}/api/mpesa/callback|" .env
    sed -i "s|CENTRAL_DOMAINS=.*|CENTRAL_DOMAINS=${REPLIT_DEV_DOMAIN},localhost,127.0.0.1|" .env
fi

# ── Install Composer dependencies if needed ───────────────────────────────────
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
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
php artisan filament:clear-cached-components 2>/dev/null || true

# ── Create storage symlink if missing ─────────────────────────────────────────
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# ── Run migrations ─────────────────────────────────────────────────────────────
echo "Running database migrations..."
php artisan migrate --force 2>/dev/null || echo "Migrations may have partially run, continuing..."

# ── Start the queue worker in background ──────────────────────────────────────
php artisan queue:work --daemon --tries=3 &>/tmp/queue.log &

# ── Start the Laravel dev server on port 8000 ────────────────────────────────
echo "Starting Laravel on port 8000..."
php artisan serve --host=0.0.0.0 --port=8000
