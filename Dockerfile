FROM php:8.3-fpm AS base

RUN apt-get update && apt-get install -y \
    nginx \
    redis-server \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    libzip-dev \
    gettext-base \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY --chown=www-data:www-data . /var/www/html

RUN rm -f /etc/nginx/sites-enabled/default
COPY docker/nginx.conf /etc/nginx/sites-enabled/default

COPY docker-entrypoint.sh /usr/local/bin/

RUN mkdir -p /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    /var/log/nginx \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# FR-1: Install Composer dependencies during build
RUN composer install --no-dev --optimize-autoloader

# FR-2: Run Laravel optimization commands during build
RUN php artisan config:cache --no-interaction \
    && php artisan route:cache --no-interaction \
    && php artisan view:cache --no-interaction \
    && php artisan event:cache --no-interaction

EXPOSE 8080

ENTRYPOINT ["docker-entrypoint.sh"]

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:${PORT:-8080}/health || exit 1
