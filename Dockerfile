FROM php:8.3-cli

WORKDIR /app

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip mbstring gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Create SQLite database file (safe fix)
RUN mkdir -p database && touch database/database.sqlite

# Allow Composer plugins
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install dependencies (avoid boot issues)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Optimize Laravel (safe)
RUN php artisan package:discover --ansi || true

# Permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# IMPORTANT Railway port
CMD php -S 0.0.0.0:$PORT -t public