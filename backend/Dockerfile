# Image de développement uniquement — pas adaptée à la production
FROM php:8.4-cli

# pdo_mysql : extension nécessaire pour que Laravel puisse parler à MySQL
RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y unzip git && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
