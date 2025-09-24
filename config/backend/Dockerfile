FROM php:8.1-apache

# Cài các extension cần thiết
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql

# Cài Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Copy source code
COPY . /var/www/html/
