# Dockerfile
FROM richarvey/nginx-php-fpm:latest

# Set working directory
WORKDIR /var/www/html

# Copy all your files to the container
COPY . .

# Set the webroot to your current directory (since index.php is in root)
ENV WEBROOT=/var/www/html

# PHP error settings
ENV PHP_ERRORS_STDERR=on
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1

# Remove default nginx config
RUN rm -rf /etc/nginx/sites-enabled/*

# Make sure all directories are readable
RUN chmod -R 755 /var/www/html

# If any directory needs write permissions (for uploads, etc.)
# RUN chmod -R 777 /var/www/html/uploads

# Expose port 80
EXPOSE 80

# Start Nginx and PHP-FPM
CMD ["/start.sh"]