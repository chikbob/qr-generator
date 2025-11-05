# --------------------------
# Базовый образ PHP 8.1
# --------------------------
FROM php:8.1-apache

# Устанавливаем зависимости и расширения
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    && docker-php-ext-install pdo_mysql zip gd \
    && a2enmod rewrite

# Копируем Composer из официального образа
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости Laravel (пропускаем dev-зависимости)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Устанавливаем правильные права на storage и bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Очищаем кэш Laravel
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

# Копируем entrypoint скрипт
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

# Делаем его исполняемым
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Используем его как точку входа
ENTRYPOINT ["docker-entrypoint.sh"]

# Открываем порт 8080
EXPOSE 8080
