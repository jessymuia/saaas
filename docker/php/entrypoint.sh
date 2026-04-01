#!/bin/sh
set -e

cd /var/www/html

php artisan filament:assets --quiet 2>/dev/null || true

chown -R www-data:www-data /var/www/html/public/css /var/www/html/public/js /var/www/html/public/fonts 2>/dev/null || true

exec php-fpm
