FROM php:7.1

# Install dependencies
RUN apt-get update && \
    apt-get upgrade -y && \
    # Install common dependencies
    apt-get install -y git zip libzip-dev && \
    docker-php-ext-configure zip --with-libzip && \
    docker-php-ext-install zip && \
    # Install composer
    php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer && \
    # Install php modules
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo_pgsql && \
    # Cleanup
    apt-get clean

WORKDIR /code

COPY composer.json /code/composer.json
COPY composer.lock /code/composer.lock

RUN composer install --no-plugins --no-scripts --no-dev

COPY .php-database-migration /code/.php-database-migration

CMD composer run migrate -- migrate:init env && \
    composer run migrate -- migrate:up env
