#!/bin/bash
set -e

# Создаем файл SQLite, если его нет
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating SQLite database file..."
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

# Запускаем миграции и сиды в продакшене без запроса подтверждения
php artisan migrate --force --seed

# Запускаем Apache в foreground
exec apache2-foreground
