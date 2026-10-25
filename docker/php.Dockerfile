FROM php:8.3-cli-alpine

RUN apk add --no-cache git unzip libpq-dev icu-dev libzip-dev $PHPIZE_DEPS \
 && docker-php-ext-install pdo_pgsql pcntl bcmath intl zip \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
EXPOSE 8000
ENTRYPOINT ["/entrypoint.sh"]
