# ============================================================
# ZEN-PAY Frontend — PHP 8.2 + Apache
# ============================================================

FROM php:8.2-apache

# mod_rewrite + proxy pour relayer /api/ vers Spring Boot (tunnel Cloudflare, LAN, etc.)
RUN a2enmod rewrite proxy proxy_http

# VirtualHost avec proxy : /api/ → conteneur zenpay-api:8080
RUN printf '<VirtualHost *:80>\n\
  DocumentRoot /var/www/public\n\
  ProxyPreserveHost On\n\
  ProxyPass /api/ http://zenpay-api:8080/api/\n\
  ProxyPassReverse /api/ http://zenpay-api:8080/api/\n\
  <Directory /var/www/public>\n\
    AllowOverride All\n\
    Require all granted\n\
  </Directory>\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

COPY public/    /var/www/public/
COPY includes/  /var/www/includes/
COPY config.php /var/www/config.php

RUN chown -R www-data:www-data /var/www

EXPOSE 80
