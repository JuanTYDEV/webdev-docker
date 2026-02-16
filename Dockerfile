FROM php:8.2-apache

# 1. Instalar dependencias del SISTEMA
# Se agregó 'libpq-dev' para que PHP pueda compilar las extensiones de PostgreSQL
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libmagickwand-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Configurar GD
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp

# 3. Instalar extensiones nativas de PHP
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mysqli \
    intl \
    gd \
    pdo_pgsql \
    pgsql

# 4. Instalar Imagick
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Habilitar mod_rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html