#!/bin/bash
set -e

# Wait for MySQL to be ready
echo "Waiting for MySQL at $DB_HOST:$DB_PORT..."
for i in {1..30}; do
    if mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" --ssl=0 -e "SELECT 1" > /dev/null 2>&1; then
        echo "MySQL is ready"
        break
    fi
    echo "MySQL not ready, waiting... ($i/30)"
    sleep 1
done

if [ ! -f /tmp/.migrations_done ]; then
    echo "Running migrations..."
    php artisan migrate --force --seed
    touch /tmp/.migrations_done
fi

php artisan config:clear
php artisan config:cache

exec apache2-foreground
