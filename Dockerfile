FROM php:8.4-cli
RUN apt-get update && apt-get install -y --no-install-recommends git unzip libzip-dev libsqlite3-dev && docker-php-ext-install pdo pdo_sqlite zip && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache database && touch database/database.sqlite && cp .env.example .env && php artisan key:generate --force && php artisan migrate --seed --force && php artisan storage:link
EXPOSE 8000
CMD ["php","artisan","serve","--host=0.0.0.0","--port=8000"]
