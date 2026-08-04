FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git unzip curl zip \
    libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install \
    pdo_mysql mbstring exif pcntl bcmath

# Redis
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# кеш зависимостей
COPY composer.json composer.lock ./

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd



RUN composer install --no-interaction --prefer-dist --optimize-autoloader || true

# код
COPY . .

# Laravel права
RUN chmod -R 775 storage bootstrap/cache


RUN apt-get update && apt-get install -y libicu-dev \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl
