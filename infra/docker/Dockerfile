# Multi-stage build for Laravel (PHP-FPM) + Vite assets

FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci || npm install
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

FROM composer:2 AS composer

FROM php:8.3-fpm-alpine
WORKDIR /var/www/html

# System deps and PHP extensions
RUN apk add --no-cache bash git libpq-dev oniguruma-dev icu-dev \
 && docker-php-ext-install pdo pdo_pgsql mbstring intl \
 && docker-php-ext-enable opcache

# Copy Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# App source
COPY . .

# Install PHP deps (prod)
RUN composer install --no-dev --prefer-dist --optimize-autoloader \
 && mkdir -p storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

# Copy built assets
COPY --from=assets /app/public/build ./public/build

ENV APP_ENV=production
EXPOSE 9000
CMD ["php-fpm"]

