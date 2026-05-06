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

# Railway may reuse cached layers or rebuild from a different Dockerfile path.
# Enforce a mod_php-compatible Apache MPM at runtime before Apache starts.
if command -v a2dismod >/dev/null 2>&1; then
    a2dismod mpm_event mpm_worker >/dev/null 2>&1 || true
fi

rm -f /etc/apache2/mods-enabled/mpm_event.conf \
      /etc/apache2/mods-enabled/mpm_event.load \
      /etc/apache2/mods-enabled/mpm_worker.conf \
      /etc/apache2/mods-enabled/mpm_worker.load

if command -v a2enmod >/dev/null 2>&1; then
    a2enmod mpm_prefork >/dev/null 2>&1 || true
fi

php artisan config:clear
php artisan config:cache

exec apache2-foreground
