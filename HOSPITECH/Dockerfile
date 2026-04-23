FROM php:8.2-apache

# 1. Installation des extensions MySQL
RUN docker-php-ext-install pdo pdo_mysql

# 2. Activation du module Rewrite (indispensable pour le routage)
RUN a2enmod rewrite

# 3. CONFIGURATION CRUCIALE : Autoriser le .htaccess
# On modifie la configuration par défaut d'Apache pour que "AllowOverride All" soit actif sur /var/www/html
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 4. Copie de ton code
COPY . /var/www/html/

# 5. Droits d'accès
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80
CMD ["apache2-foreground"]