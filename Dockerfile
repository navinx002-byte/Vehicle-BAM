FROM php:8.2-apache

# Install MariaDB, curl, tar, and required PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    mariadb-server \
    mariadb-client \
    curl \
    tar \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy source code into Apache docroot
COPY . /var/www/html/

# Download and install phpMyAdmin into /var/www/html/phpmyadmin
RUN curl -sL https://files.phpmyadmin.net/phpMyAdmin/5.2.1/phpMyAdmin-5.2.1-english.tar.gz | tar -xz -C /var/www/html/ \
    && mv /var/www/html/phpMyAdmin-5.2.1-english /var/www/html/phpmyadmin \
    && cp /var/www/html/phpmyadmin-config.inc.php /var/www/html/phpmyadmin/config.inc.php \
    && mkdir -p /var/www/html/phpmyadmin/tmp \
    && chmod 777 /var/www/html/phpmyadmin/tmp

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/uploads

# Configure Apache to listen to PORT env var
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Setup entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

ENV PORT=80
EXPOSE 80

CMD ["/usr/local/bin/entrypoint.sh"]
