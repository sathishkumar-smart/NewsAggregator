FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip curl git \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

# Permissions
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

EXPOSE 9000

CMD ["php-fpm"]
