FROM php:8.2-apache

RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork

RUN docker-php-ext-install pdo pdo_mysql mysqli

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

# DEBUG sementara
CMD ["sh", "-c", "echo '--- mods-enabled ---'; ls -l /etc/apache2/mods-enabled | grep -i mpm; echo '--- LoadModule mpm ---'; grep -rn 'mpm_' /etc/apache2 --include=*.conf --include=*.load | grep -i LoadModule; echo '--- end ---'; sleep 30; apache2-foreground"]
