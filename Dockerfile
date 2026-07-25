FROM php:8.3-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install MySQL PHP extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy website files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80