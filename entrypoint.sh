#!/bin/bash
set -e

echo "=== Starting AutoCare Service Deployment Container ==="

# Prepare MariaDB runtime directories
mkdir -p /var/run/mysqld /var/log/mysql /var/lib/mysql
chown -R mysql:mysql /var/run/mysqld /var/log/mysql /var/lib/mysql
chmod 777 /var/run/mysqld

# If no external DB_HOST provided (or if localhost/127.0.0.1), use built-in MariaDB
if [ -z "$DB_HOST" ] || [ "$DB_HOST" = "localhost" ] || [ "$DB_HOST" = "127.0.0.1" ]; then
    echo "Starting internal MariaDB server..."
    
    # Initialize DB directory if needed
    if [ ! -d "/var/lib/mysql/mysql" ]; then
        mariadb-install-db --user=mysql --datadir=/var/lib/mysql > /dev/null 2>&1 || true
    fi

    # Start MariaDB in background
    /usr/bin/mysqld_safe --user=mysql --datadir=/var/lib/mysql &

    # Wait for MariaDB to be ready
    echo "Waiting for MariaDB to start..."
    for i in {1..30}; do
        if mysqladmin ping --silent 2>/dev/null; then
            echo "MariaDB is up and running!"
            break
        fi
        sleep 1
    done

    # Setup database and user
    echo "Setting up vehicle_service_db schema..."
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS vehicle_service_db;" 2>/dev/null || true
    mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '' WITH GRANT OPTION; FLUSH PRIVILEGES;" 2>/dev/null || true
    mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' IDENTIFIED BY '' WITH GRANT OPTION; FLUSH PRIVILEGES;" 2>/dev/null || true

    # Import schema if tables are not yet created
    TABLE_COUNT=$(mysql -u root -N -s -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='vehicle_service_db';" 2>/dev/null || echo "0")
    if [ "$TABLE_COUNT" -eq 0 ] && [ -f /var/www/html/vehicle_service_db.sql ]; then
        echo "Importing vehicle_service_db.sql..."
        mysql -u root vehicle_service_db < /var/www/html/vehicle_service_db.sql 2>/dev/null || true
        echo "Database schema successfully imported!"
    else
        echo "Database already has $TABLE_COUNT tables."
    fi
fi

# Ensure uploads directory is writable
chmod -R 777 /var/www/html/uploads 2>/dev/null || true

echo "Starting Apache on port ${PORT:-80}..."
exec apache2-foreground
