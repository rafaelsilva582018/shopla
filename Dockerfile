# syntax=docker/dockerfile:1

FROM node:22-alpine AS assets
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM php:8.4-fpm-bookworm AS app
WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        git \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        unzip \
        zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        opcache \
        pdo_pgsql \
        pgsql \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader \
    --prefer-dist

COPY . .
COPY --from=assets /app/public/build ./public/build
COPY docker/production/php.ini /usr/local/etc/php/conf.d/99-shopla.ini
COPY docker/production/entrypoint.sh /usr/local/bin/shopla-entrypoint

RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi \
    && mkdir -p public/vendor/livewire \
    && cp -R vendor/livewire/livewire/dist/* public/vendor/livewire/ \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x /usr/local/bin/shopla-entrypoint

ENTRYPOINT ["shopla-entrypoint"]
CMD ["php-fpm"]

FROM nginx:1.27-alpine AS nginx
WORKDIR /var/www/html

COPY docker/production/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public ./public
