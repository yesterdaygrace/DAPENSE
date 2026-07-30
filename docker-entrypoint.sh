#!/bin/sh
set -e

# Copy nginx config if not exists
if [ ! -f /etc/nginx/sites-enabled/default ]; then
    cp /var/www/html/docker/nginx.conf /etc/nginx/sites-enabled/default
fi

# Create nginx temp directories (tmpfs mounts are empty at boot)
mkdir -p /var/lib/nginx/body /var/lib/nginx/proxy /var/lib/nginx/fastcgi /var/lib/nginx/client_body
chown -R www-data:www-data /var/lib/nginx

# Create Laravel storage directories (some are tmpfs mounts)
mkdir -p /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Start php-fpm in background
php-fpm -D

# Start nginx in foreground
exec nginx -g 'daemon off;'
