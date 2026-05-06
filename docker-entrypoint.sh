#!/bin/bash
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    echo "vendor/autoload.php is missing. The image was built without Composer dependencies."
    exit 1
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

if [ -n "$APP_KEY" ]; then
    php artisan config:cache
else
    echo "APP_KEY is not set. Skipping config cache."
fi

if [ -n "$DB_HOST" ] && [ -n "$DB_PORT" ] && [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
    echo "Waiting for MySQL at $DB_HOST:$DB_PORT..."
    db_ready=false

    for i in {1..30}; do
        if mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" --ssl=0 -e "SELECT 1" > /dev/null 2>&1; then
            echo "MySQL is ready"
            db_ready=true
            break
        fi

        echo "MySQL not ready, waiting... ($i/30)"
        sleep 1
    done

    if [ "$db_ready" = true ] && [ ! -f /tmp/.migrations_done ]; then
        echo "Running migrations..."
        php artisan migrate --force --seed
        touch /tmp/.migrations_done
    elif [ "$db_ready" != true ]; then
        echo "MySQL is still unavailable after 30 attempts. Skipping migrations for this start."
    fi
else
    echo "Database variables are incomplete. Skipping DB wait and migrations."
fi

exec apache2-foreground
