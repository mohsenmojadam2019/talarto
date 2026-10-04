FROM php:8.4-cli
RUN apt-get update && apt-get install -y git unzip libzip-dev libsqlite3-dev && docker-php-ext-install pdo pdo_sqlite zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer install --no-interaction --prefer-dist
RUN mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache database && touch database/database.sqlite
RUN cp .env.example .env && php artisan key:generate --force && php artisan migrate --force --seed
EXPOSE 8000
CMD ["php","artisan","serve","--host=0.0.0.0","--port=8000"]
