# Используем официальный PHP образ с расширениями
FROM php:8.2-fpm

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev zip curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Устанавливаем Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Копируем проект
WORKDIR /var/www/html
COPY . .

# Устанавливаем зависимости Laravel
RUN composer install --no-dev --optimize-autoloader

# Настраиваем права
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Устанавливаем порт и команду запуска
EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=8080
