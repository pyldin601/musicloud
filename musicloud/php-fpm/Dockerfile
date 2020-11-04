FROM php:7.1-fpm

# Install dependencies
RUN apt-get update && \
    apt-get upgrade -y && \

    apt-get install -y git ffmpeg && \

    php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer && \

    apt-get install -y libpq-dev && \
    docker-php-ext-install -j$(nproc) pdo_pgsql

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /code

COPY composer.json /code/composer.json
COPY composer.lock /code/composer.lock

RUN composer install

COPY public/index.php /code/public/index.php
COPY app /code/app
COPY config /code/config
