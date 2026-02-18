FROM php:8.2-apache

# ----------------------------
# Установка системных пакетов
# ----------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl gnupg \
    git unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    pkg-config default-mysql-client \
    libmagickwand-dev imagemagick \
    && rm -rf /var/lib/apt/lists/*

# ----------------------------
# Установка Node.js 20
# ----------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# ----------------------------
# PHP расширения
# ----------------------------
RUN docker-php-ext-install pdo_mysql zip gd

# Установка Imagick через PECL
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Включаем mod_rewrite
RUN a2enmod rewrite

# ----------------------------
# Composer
# ----------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ----------------------------
# Laravel зависимости
# ----------------------------
COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-progress || true

COPY package.json package-lock.json ./
RUN npm install

COPY . .

# ----------------------------
# Права
# ----------------------------
RUN mkdir -p database storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache

# ----------------------------
# Apache config
# ----------------------------
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/html>/,/<\/Directory>/d' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    AllowOverride All' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    Require all granted' >> /etc/apache2/sites-available/000-default.conf \
    && echo '</Directory>' >> /etc/apache2/sites-available/000-default.conf

# ----------------------------
# Entrypoint
# ----------------------------
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]

EXPOSE 80
CMD ["apache2-foreground"]
