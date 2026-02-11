# Use official PHP with Apache
FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy project files into Apache web root
COPY ./public/ /var/www/html/

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies (PHPMailer, etc.)
COPY composer.json /var/www/html/
WORKDIR /var/www/html
RUN composer install
