FROM php:8.2-apache

# Matikan semua MPM yang aktif, lalu aktifkan hanya prefork
RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
    && a2enmod mpm_prefork

# Install ekstensi PHP untuk MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy project
COPY . /var/www/html/

# Permission
RUN chown -R www-data:www-data /var/www/html

# Apache listen di port 80
EXPOSE 80
