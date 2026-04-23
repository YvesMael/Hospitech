FROM php:8.2-apache

# 1. Mise à jour des dépôts et installation des dépendances système pour Postgres
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# 2. Activation du module Rewrite (indispensable pour ton api.php)
RUN a2enmod rewrite

# 3. Autoriser le .htaccess (Configuration Apache)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 4. Copie de ton code
COPY . /var/www/html/

# 5. Droits d'accès
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80
CMD ["apache2-foreground"]