FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    zip \
    unzip \
    git \
    supervisor \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions fast using mlocati
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo pdo_mysql pdo_pgsql opcache bcmath gd xml zip mbstring pcntl

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy ONLY composer files first to leverage Docker layer caching
COPY composer.json composer.lock ./

# Install PRE dependenceis without autoloader/scripts
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application code
COPY . .

# Finish composer setup WITH autoloader optimization
RUN composer install --no-dev --optimize-autoloader

# Setup Nginx configuration
RUN rm -f /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Setup Supervisor and PHP configurations
COPY supervisord.conf /etc/supervisor/supervisord.conf
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Fix permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 80

# Setup entrance script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
