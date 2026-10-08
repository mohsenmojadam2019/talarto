#!/bin/sh
set -eu

mkdir -p /app/database /app/storage/logs /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views
touch /app/database/database.sqlite

# Generate and retain a development encryption key; production must supply APP_KEY.
if [ -z "${APP_KEY:-}" ]; then
    if [ "${APP_ENV:-local}" = "production" ]; then
        echo "APP_KEY is mandatory in production" >&2
        exit 1
    fi
    if [ -s /app/database/.app-key ]; then
        APP_KEY=$(cat /app/database/.app-key)
    else
        APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
        printf '%s' "$APP_KEY" > /app/database/.app-key
        chmod 600 /app/database/.app-key
    fi
    export APP_KEY
fi

mkdir -p /app/database/migrations
cp -a /app/migration-source/. /app/database/migrations/
php artisan migrate --force
if [ "${SEED_DEMO:-false}" = "true" ]; then
    php artisan talarto:seed-demo-if-empty
fi
php artisan storage:link >/dev/null 2>&1 || true
exec "$@"
