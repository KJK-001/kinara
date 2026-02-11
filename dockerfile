FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install required tools and libraries for Composer and PHPMailer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
 && docker-php-ext-install zip \
 && rm -rf /var/lib/apt/lists/*

# Copy project files into Apache web root
COPY ./ /var/www/html/

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies if composer.json exists
RUN test -f composer.json && composer install --no-dev --optimize-autoloader || true

EXPOSE 80
