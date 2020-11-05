FROM php:7.1-fpm

ARG MAX_UPLOAD_FILESIZE=256
ENV MAX_UPLOAD_FILESIZE=${MAX_UPLOAD_FILESIZE}

# Install dependencies
RUN apt-get update && \
    apt-get upgrade -y && \
    # Install common dependencies
    apt-get install -y git ffmpeg zip libzip-dev && \
    docker-php-ext-configure zip --with-libzip && \
    docker-php-ext-install zip && \
    # Install composer
    php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer && \
    # Install php modules
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo_pgsql && \
    # Cleanup
    apt-get clean && \
    # Use the default production configuration
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    # Patch php configuration files
    sed -i "s/^upload_max_filesize\s=.*/upload_max_filesize = ${MAX_UPLOAD_FILESIZE}M/" $PHP_INI_DIR/php.ini && \
    sed -i "s/^post_max_size\s=.*/post_max_size = ${MAX_UPLOAD_FILESIZE}M/" $PHP_INI_DIR/php.ini

WORKDIR /code

COPY composer.json /code/composer.json
COPY composer.lock /code/composer.lock

RUN composer install --no-plugins --no-scripts --no-dev

COPY public/index.php /code/public/index.php
COPY app /code/app
COPY config /code/config
