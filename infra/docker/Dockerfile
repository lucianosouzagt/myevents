FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci || npm install
COPY public ./public
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build


FROM composer:2 AS composer


FROM php:8.3-fpm-alpine
WORKDIR /var/www/html

# System deps + GD + ZIP
RUN apk add --no-cache \
    bash git libpq-dev oniguruma-dev icu-dev \
    freetype-dev libjpeg-turbo-dev libpng-dev \
    libzip-dev zip unzip \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    intl \
    gd \
    zip \
 && docker-php-ext-enable opcache

# Evita o "dubious ownership"
RUN git config --global --add safe.directory /var/www/html

# Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# 👉 Copia primeiro só os arquivos do composer (cache inteligente)
COPY composer.json composer.lock ./

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Agora copia o resto da app
COPY . .

RUN mkdir -p storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

# Assets
COPY --from=assets /app/public/build ./public/build

ENV APP_ENV=production
EXPOSE 9000
CMD ["php-fpm"]
