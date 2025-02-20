FROM php:8.2-fpm

# Install system dependencies (including cron and supervisor)
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    supervisor \
    cron

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Node.js (using Node 18.x)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application code
COPY . /var/www

# Copy Supervisor configuration file(s)
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy the entrypoint script and ensure it's executable
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Ensure the schedule-run script is executable (if used)
RUN chmod +x /var/www/schedule-run.sh

# Expose port 9000 (for PHP-FPM)
EXPOSE 9000

# Use the entrypoint script as the container's entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
