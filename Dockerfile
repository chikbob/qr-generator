FROM php:8.3-apache

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

# Фиксируем совместимый MPM для mod_php и включаем rewrite
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite

# ----------------------------
# Composer
# ----------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ----------------------------
# Laravel зависимости
# ----------------------------
COPY composer.json composer.lock ./
RUN composer install \
    --no-interaction \
    --no-dev \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

# ----------------------------
# Права
# ----------------------------
RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi \
    && npm run build \
    && mkdir -p database storage bootstrap/cache public/qr_codes \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chown -R www-data:www-data public/qr_codes \
    && chmod -R 775 storage bootstrap/cache public/qr_codes

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
