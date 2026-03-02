FROM php:8.4-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Habilitar extensões PHP necessárias para Laravel
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /app

# Copiar arquivos do projeto
COPY . /app

# Criar e configurar permissões dos diretórios ANTES de instalar dependências
RUN mkdir -p storage/app storage/framework storage/logs bootstrap/cache && \
    chown -R www-data:www-data /app && \
    chmod -R 755 /app && \
    chmod -R 777 storage bootstrap/cache

# Instalar dependências PHP
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Garantir permissões corretas após composer
RUN chown -R www-data:www-data /app && \
    chmod -R 755 bootstrap storage && \
    chmod -R 777 bootstrap/cache storage/logs storage/app

# Expor porta
EXPOSE 9000

CMD ["php-fpm"]
