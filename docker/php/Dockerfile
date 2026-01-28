FROM php:8.2-cli

# Dependencias
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiamos TODO el sistema dentro de la imagen
COPY . .

# Optimización Laravel
RUN composer install --no-dev --optimize-autoloader


EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
