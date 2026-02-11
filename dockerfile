FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy project files into Apache web root
COPY ./ /var/www/html/

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies if composer.json exists
RUN if [ -f composer.json ]; then composer install; fi

EXPOSE 80
