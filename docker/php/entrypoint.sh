#!/bin/sh
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] vendor/ not found — running composer install..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

echo "[entrypoint] Publishing Filament assets..."
php artisan filament:assets --quiet 2>/dev/null || true

chown -R www-data:www-data \
    /var/www/html/public/css \
    /var/www/html/public/js \
    /var/www/html/public/fonts \
    2>/dev/null || true

echo "[entrypoint] Starting php-fpm..."
exec docker-php-entrypoint php-fpm
