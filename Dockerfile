# Use multi-architecture base image
FROM unit:1.34.1-php8.3

# Install system dependencies and PHP extensions
RUN apt update && apt install -y \
    curl unzip git libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libssl-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pcntl opcache pdo pdo_pgsql pgsql pdo_mysql intl zip gd exif ftp bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt clean \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP
RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit_buffer_size=256M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize=64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/custom.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Create necessary directories
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# Copy application code
COPY . .

# Install PHP dependencies
RUN composer install --prefer-dist --optimize-autoloader --no-interaction --no-dev

# Set proper permissions
RUN chown -R unit:unit /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Copy Unit configuration
COPY unit.json /docker-entrypoint.d/unit.json

# Run migrations
RUN php artisan migrate --force

# Update port to match docker-compose
EXPOSE 80

# Start Unit daemon
CMD ["unitd", "--no-daemon"]