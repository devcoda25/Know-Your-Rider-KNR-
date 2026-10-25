#!/bin/sh
# Shared dev entrypoint for every Laravel service container.
set -e
cd /var/www

[ -f .env ] || cp .env.example .env

composer install --no-interaction --prefer-dist

grep -q '^APP_KEY=.' .env || php artisan key:generate --force --no-interaction

if [ "$RUN_MIGRATIONS" = "1" ]; then
  php artisan migrate --force --seed --no-interaction
fi

exec php artisan serve --host=0.0.0.0 --port=8000
