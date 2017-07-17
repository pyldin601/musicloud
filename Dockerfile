FROM pldin601/musicloud-image

MAINTAINER Roman Lakhtadyr <roman.lakhtadyr@gmail.com>

RUN apt-get update && \
    apt-get install -y cron && \
    apt-get clean && \
    (echo '* * * * * root curl -X POST http://guest:please@localhost:8080/cron' > /etc/cron.d/php-cron) && \
    chmod 0644 /etc/cron.d/php-cron && \
    touch /var/log/cron.log && \
    cron

WORKDIR /usr/app/

COPY composer.json composer.lock ./
RUN composer install --no-plugins --no-scripts --no-dev

COPY package.json package-lock.json ./
RUN npm install

COPY . ./

ARG GIT_CURRENT_COMMIT="<unknown>"
ENV GIT_CURRENT_COMMIT=${GIT_CURRENT_COMMIT}
