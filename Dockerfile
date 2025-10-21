# --------------------------
# Базовый образ PHP с FPM
# --------------------------
FROM php:8.2-fpm

# --------------------------
# Установка системных зависимостей
# --------------------------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    nano \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd

# --------------------------
# Установка Composer
# --------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --------------------------
# Установка Node.js (для Vite)
# --------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

# --------------------------
# Создание рабочей директории
# --------------------------
WORKDIR /var/www/html

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости Laravel и Node
RUN composer install --no-interaction --optimize-autoloader
RUN npm install
RUN npm run build

# Копируем права на storage
RUN chown -R www-data:www-data storage bootstrap/cache

# --------------------------
# Порт
# --------------------------
EXPOSE 8080

# --------------------------
# Команда запуска Laravel сервера
# --------------------------
CMD php artisan serve --host=0.0.0.0 --port=8080
