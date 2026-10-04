FROM php:8.2-apache

# Hapus semua MPM Apache yang aktif
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
          /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork

# Install ekstensi PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy project
COPY . /var/www/html/

# Permission
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
