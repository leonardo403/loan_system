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

# Copiar arquivos composer primeiro (melhor cache do Docker)
COPY composer.json composer.lock* ./

# Instalar dependências PHP
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist \
    && composer cache:clear

# Copiar resto dos arquivos do projeto
COPY . .

# Criar e configurar permissões dos diretórios
RUN mkdir -p storage/app storage/framework storage/logs bootstrap/cache

# Garantir permissões corretas
RUN chown -R www-data:www-data /app && \
    chmod -R 755 /app && \
    chmod -R 777 bootstrap/cache storage/logs storage/app

# Expor porta
EXPOSE 9000

CMD ["php-fpm"]
