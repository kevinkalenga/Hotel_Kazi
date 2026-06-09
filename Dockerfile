FROM php:8.3-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip mbstring gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN mkdir -p database && touch database/database.sqlite

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

RUN php artisan package:discover --ansi || true

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=$PORT