FROM php:8.2-fpm

# System deps
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpq-dev libonig-dev \
 && docker-php-ext-install intl pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app code (the bind mount in docker-compose will override this in dev)
COPY . .

# Fix ownership and permissions for Symfony
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Install PHP deps if composer files exist (keeps build fast on code changes)
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --no-progress

# PHP-FPM listens on 9000 by default
EXPOSE 9000

CMD ["php-fpm"]