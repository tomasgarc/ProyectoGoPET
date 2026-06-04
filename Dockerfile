FROM php:8.3-apache

# Instalar dependencias del sistema y extensiones de PHP necesarias para Laravel y SQLite
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_sqlite

# Habilitar el módulo rewrite de Apache
RUN a2enmod rewrite

# Reconfigurar el DocumentRoot de Apache para apuntar a la carpeta /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar Composer desde la imagen oficial para la gestión de dependencias PHP
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar el código fuente del proyecto al contenedor
COPY . .

# Establecer permisos adecuados para el servidor web (storage y bootstrap/cache)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto 80 para el tráfico HTTP
EXPOSE 80

# Iniciar Apache en primer plano
CMD ["apache2-foreground"]
