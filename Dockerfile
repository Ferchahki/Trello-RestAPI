FROM php:8.0-fpm-alpine

RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev

CMD php artisan serve --host=0.0.0.0 --port=80
