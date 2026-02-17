FROM php:8.1-apache

# Установка Node.js
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libpng-dev libonig-dev libxml2-dev libzip-dev pkg-config \
    && docker-php-ext-install pdo_mysql zip gd \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install --no-interaction --optimize-autoloader --no-dev --no-progress || true

COPY package.json package-lock.json ./

RUN npm install

COPY . .

RUN mkdir -p database storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/html>/,/<\/Directory>/d' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    AllowOverride All' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    Require all granted' >> /etc/apache2/sites-available/000-default.conf \
    && echo '</Directory>' >> /etc/apache2/sites-available/000-default.conf

RUN echo '#!/bin/bash\n\
set -e\n\
\n\
if [ ! -f /tmp/.migrations_done ]; then\n\
    echo "Running migrations..."\n\
    php artisan migrate --force --seed\n\
    touch /tmp/.migrations_done\n\
fi\n\
\n\
php artisan config:clear\n\
php artisan config:cache\n\
\n\
exec apache2-foreground' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
EXPOSE 80

CMD ["apache2-foreground"]
