#!/bin/bash
set -e

# If no remote DB is provided, spin up internal MariaDB and import database
if [ -z "$DB_HOST" ] || [ "$DB_HOST" = "localhost" ] || [ "$DB_HOST" = "127.0.0.1" ]; then
    echo "Starting local MariaDB service..."
    service mariadb start || /etc/init.d/mariadb start

    # Wait for MariaDB to be responsive
    for i in {1..30}; do
        if mysqladmin ping --silent; then
            break
        fi
        sleep 1
    done

    echo "Initializing database..."
    mysql -e "CREATE DATABASE IF NOT EXISTS vehicle_service_db;"
    if [ -f /var/www/html/vehicle_service_db.sql ]; then
        mysql vehicle_service_db < /var/www/html/vehicle_service_db.sql || true
        echo "Database imported successfully."
    fi
fi

# Ensure uploads directory is writable
chmod -R 777 /var/www/html/uploads 2>/dev/null || true

# Start Apache
echo "Starting Apache on port ${PORT:-80}..."
exec apache2-foreground
