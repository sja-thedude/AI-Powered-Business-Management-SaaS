# syntax=docker/dockerfile:1

# ---------- Stage 1: build frontend assets (Inertia + Vue + Tailwind) ----------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json vite.config.js ./
RUN npm ci
COPY resources ./resources
COPY public ./public
# Ziggy/route() resolves at runtime from window.Ziggy, so we can build now.
RUN npm run build

# ---------- Stage 2: PHP dependencies ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# ---------- Stage 3: runtime (php-fpm) ----------
FROM php:8.2-fpm-alpine AS runtime

# System deps + PHP extensions needed by Laravel 12 + Postgres + Redis.
RUN apk add --no-cache \
        bash git icu-dev libzip-dev postgresql-dev oniguruma-dev linux-headers $PHPIZE_DEPS \
    && docker-php-ext-install pdo_pgsql pgsql bcmath pcntl intl zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

WORKDIR /var/www/html

# App source + composer vendor + built assets.
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Finalize autoloader and harden permissions.
RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/php/php.ini /usr/local/etc/php/conf.d/novabiz.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 9000
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
