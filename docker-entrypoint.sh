#!/bin/sh
set -e

# Copy nginx config if not exists
if [ ! -f /etc/nginx/sites-enabled/default ]; then
    cp /var/www/html/docker/nginx.conf /etc/nginx/sites-enabled/default
fi

# Start php-fpm in background
php-fpm -D

# Start nginx in foreground
exec nginx -g 'daemon off;'
