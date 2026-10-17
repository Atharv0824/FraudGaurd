# FraudGuard Docker Container for 1-Click Cloud Deployment (Render, Railway, Fly.io)
FROM php:8.2-apache

# Install PDO SQLite and MySQL extensions
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql

# Enable Apache mod_rewrite for clean URLs and routing
RUN a2enmod rewrite

# Copy project files into web root
COPY . /var/www/html/

# Set working directory & permissions for SQLite database read/write
WORKDIR /var/www/html
RUN mkdir -p /var/www/html/database && \
    chmod -R 777 /var/www/html/database && \
    chown -R www-data:www-data /var/www/html

# Expose default HTTP port
EXPOSE 80

CMD ["apache2-foreground"]
