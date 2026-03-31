FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y libicu-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    posix \
    bcmath \
    gd \
    zip \
    intl

RUN pecl install redis && docker-php-ext-enable redis

# Add PHP configuration for timeouts and memory
RUN echo 'max_execution_time = 300' >> /usr/local/etc/php/conf.d/timeout.ini && \
    echo 'default_socket_timeout = 60' >> /usr/local/etc/php/conf.d/timeout.ini && \
    echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/timeout.ini

# Add PHP-FPM configuration for process pool
RUN echo 'request_terminate_timeout = 300' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_children = 20' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.start_servers = 5' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.min_spare_servers = 5' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_spare_servers = 15' >> /usr/local/etc/php-fpm.d/www.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache