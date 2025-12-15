FROM php:8.0-apache

# Cài extension cần cho Laravel
RUN docker-php-ext-install pdo pdo_mysql

# Enable rewrite
RUN a2enmod rewrite

# Set thư mục web
WORKDIR /var/www/html

# Copy source code
COPY . .

# Set quyền
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Apache config
COPY ./.docker/vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
