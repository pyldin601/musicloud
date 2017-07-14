FROM debian:jessie-backports

MAINTAINER Roman Lakhtadyr <roman.lakhtadyr@gmail.com>

ENV DEBIAN_FRONTEND=noninteractive
ENV PHP_VERSION=7.1
ENV PHP_ENV=production
ENV MAX_UPLOAD_FILESIZE=100

# Install utilities
RUN apt-get update && \
    apt-get install -y curl apt-transport-https git && \
    rm -rf /var/lib/apt/lists/*

# Install node.js, npm, web server and media applications
RUN (curl -sL https://deb.nodesource.com/setup_8.x | bash) && \
    apt-get install -y ffmpeg nodejs nginx supervisor && \
    rm -rf /var/lib/apt/lists/*

# Install php and composer
RUN (curl -sL https://packages.sury.org/php/apt.gpg | apt-key add -) && \
    (echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list) && \
    apt-get update && \
    apt-get install -y \
        php$PHP_VERSION-fpm \
        php$PHP_VERSION-cli \
        php$PHP_VERSION-mbstring \
        php$PHP_VERSION-pgsql \
        php$PHP_VERSION-xml \
        php$PHP_VERSION-gd \
        php$PHP_VERSION-mcrypt \
        php$PHP_VERSION-curl \
        php$PHP_VERSION-zip && \
    rm -rf /var/lib/apt/lists/* && \

    mkdir -p /var/run/php && \

    (curl -sL https://getcomposer.org/installer | php -- --install-dir=bin --filename=composer) 

# Patch configuration files
RUN sed -i "s/^upload_max_filesize\s=.*/upload_max_filesize = ${MAX_UPLOAD_FILESIZE}M/" /etc/php/$PHP_VERSION/fpm/php.ini && \
    sed -i "s/^post_max_size\s=.*/post_max_size = ${MAX_UPLOAD_FILESIZE}M/" /etc/php/$PHP_VERSION/fpm/php.ini && \
    sed -i 's/^variables_order\s=.*/variables_order = "EGPCS"/' /etc/php/$PHP_VERSION/fpm/php.ini && \
    sed -i "/^;clear_env/s/^;//" /etc/php/$PHP_VERSION/fpm/pool.d/www.conf && \
    echo "client_max_body_size ${MAX_UPLOAD_FILESIZE}m;" > /etc/nginx/conf.d/nginx-upload.conf

# Copy configuration files
COPY ./docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY ./docker/nginx-fpm.conf /etc/nginx/sites-available/nginx-fpm.conf

# Configure nginx
RUN rm -f /etc/nginx/sites-enabled/* && \
    ln -s /etc/nginx/sites-available/nginx-fpm.conf /etc/nginx/sites-enabled/nginx-fpm.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

# Install application
WORKDIR /usr/app/
COPY composer.json composer.lock ./
RUN composer install --no-plugins --no-scripts --no-dev

COPY . ./
VOLUME /tmp

EXPOSE 8080

ARG GIT_CURRENT_COMMIT="<unknown>"
ENV GIT_CURRENT_COMMIT=${GIT_CURRENT_COMMIT}
