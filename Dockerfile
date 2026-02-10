FROM php:8.2-apache

# 1. Instalar dependencias del SISTEMA
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libmagickwand-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Configurar GD para que acepte JPEG y FreeType
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp

# 3. Instalar extensiones nativas de PHP
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli intl gd

# 4. Instalar Imagick desde PECL
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Configurar el directorio de trabajo
WORKDIR /var/www/html

