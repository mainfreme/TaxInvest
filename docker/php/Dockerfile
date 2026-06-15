FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    icu-dev \
    libpq-dev \
    rabbitmq-c-dev \
    linux-headers \
    git \
    unzip

RUN docker-php-ext-install \
    intl \
    opcache \
    pdo_pgsql \
    pgsql

RUN pecl install amqp \
    && docker-php-ext-enable amqp

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-taxinvest.ini

EXPOSE 9000

CMD ["php-fpm"]
