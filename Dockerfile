FROM php:8.2-apache

# Hapus semua MPM yang aktif, lalu aktifkan hanya prefork
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork

# (opsional) cek saat build, harus cuma 1 MPM
RUN apache2ctl -M 2>/dev/null | grep mpm

# Install ekstensi PHP untuk MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy project
COPY . /var/www/html/

# Permission
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
