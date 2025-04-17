# Use the official PHP image with CLI and install dependencies
FROM php:8.2-cli

# Install required system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql
RUN apt-get update && apt-get install -y cron

# Copy the php.ini-production to php.ini
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Set upload_max_filesize and post_max_size in php.ini
RUN echo "upload_max_filesize = 500M" >> /usr/local/etc/php/php.ini \
    && echo "post_max_size = 500M" >> /usr/local/etc/php/php.ini

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y vim

# Set the working directory to /var/www
WORKDIR /var/www

# Copy the application files into the container
COPY . .

# Create a crontab file and copy it into the image
COPY crontab.txt /etc/cron.d/laravel-cron

# Set permissions for the cron file
RUN chmod 0644 /etc/cron.d/laravel-cron

# Apply crontab to the cron system
RUN crontab /etc/cron.d/laravel-cron

# Create the log file to track cron job output
RUN touch /var/www/output.txt

# Start the cron service and keep it running in the foreground
CMD service cron start && tail -f /dev/null

RUN crontab /etc/cron.d/laravel-cron

CMD ["cron", "-f"]

# give permisson to public
RUN chmod -R 777 public


# Set proper file permissions for storage and cache directories
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Check if the .env file is present (needed for key generation)
RUN if [ -f .env ]; then echo "Found .env file"; else echo "No .env file"; exit 1; fi

# Generate the Laravel app's key (if possible)
RUN php artisan key:generate || echo "Laravel key generation failed!"

# Expose port 8000 for the app to be accessible
EXPOSE 8000

# Start the Laravel application by running Composer install and then Artisan serve
CMD composer install --no-interaction --optimize-autoloader --no-dev && php artisan serve --host=0.0.0.0 --port=8000
