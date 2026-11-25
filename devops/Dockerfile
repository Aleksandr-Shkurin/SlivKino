# Frontend build: Vite 7 requires a modern Node.js runtime.
FROM node:22-bookworm-slim AS frontend

WORKDIR /app
COPY package.json ./
RUN npm install --no-audit --no-fund
COPY . .
RUN npm run build

# Laravel runtime.
FROM php:8.3-cli-bookworm AS app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies first to keep this layer cached when only app code changes.
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts

COPY . .

RUN composer dump-autoload --no-dev --optimize --no-interaction --no-scripts \
    && php artisan package:discover --ansi \
    && mkdir -p public/images/posters /var/lib/slivkino \
    && chmod -R ug+rwX storage bootstrap/cache

COPY --from=frontend /app/public/build ./public/build
COPY devops/entrypoint.sh /usr/local/bin/slivkino-entrypoint
RUN chmod +x /usr/local/bin/slivkino-entrypoint

EXPOSE 8000
ENTRYPOINT ["slivkino-entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
