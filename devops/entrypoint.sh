#!/bin/sh
set -eu

cd /var/www/html

# The .env file is mounted from the project directory by Compose.
if [ ! -f .env ]; then
    cp .env.example .env
fi

DB_FILE="${DB_DATABASE:-/var/lib/slivkino/database.sqlite}"
mkdir -p "$(dirname "$DB_FILE")"
if [ ! -f "$DB_FILE" ]; then
    touch "$DB_FILE"
fi

# Generate an application key once and persist it in the host .env file.
if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi

# Migrations are safe to run on each start; Laravel tracks completed migrations.
php artisan migrate --force

# This project's seeder uses create() for several records, so seed only once
# per persistent test database volume to avoid duplicate cinemas and movies.
SEED_MARKER="/var/lib/slivkino/.seeded"
if [ ! -f "$SEED_MARKER" ]; then
    php artisan db:seed --force
    touch "$SEED_MARKER"
fi

exec "$@"
