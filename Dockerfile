# --------------------------
# Базовый образ PHP 8.1
# --------------------------
FROM php:8.1-apache

# Устанавливаем расширения и Composer
RUN apt-get update && apt-get install -y git unzip libpng-dev libonig-dev libxml2-dev zip && \
    docker-php-ext-install pdo_mysql zip gd && \
    a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости Laravel
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Разрешаем Apache .htaccess
RUN chown -R www-data:www-data storage bootstrap/cache

# Laravel настройки
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

EXPOSE 8080

# Запуск Apache на 8080
CMD ["apache2-foreground"]
