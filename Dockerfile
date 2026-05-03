FROM php:8.2-apache

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Habilitar mod_rewrite de Apache (útil para rutas amigables en el futuro)
RUN a2enmod rewrite

# Copiar el código del proyecto al contenedor
COPY . /var/www/html/

# Ajustar permisos para que Apache pueda escribir (necesario para subida de fotos)
RUN chown -R www-data:www-data /var/www/html/ && chmod -R 755 /var/www/html/

EXPOSE 80
