# PHP 8.4 FPM
FROM php:8.4-fpm-alpine

# Arguments for user permissions
ARG UID=1000
ARG GID=1000

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create non-root user
RUN addgroup -g ${GID} www && \
    adduser -u ${UID} -G www -s /bin/sh -D www

# Set working directory
WORKDIR /var/www/html

# Copy existing application
COPY --chown=www:www . .

# Install Node.js and NPM
RUN apk add --no-cache nodejs npm

# Copy Entrypoint Script
COPY .docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Switch to non-root user
USER www

# Expose port 9000 for PHP-FPM
EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
