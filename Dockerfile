FROM pldin601/musicloud-base

MAINTAINER Roman Lakhtadyr <roman.lakhtadyr@gmail.com>

WORKDIR /usr/app/

COPY composer.json composer.lock ./
RUN composer install --no-plugins --no-scripts --no-dev

COPY package.json package-lock.json ./
RUN npm install

COPY . ./

ARG GIT_CURRENT_COMMIT="<unknown>"
ENV GIT_CURRENT_COMMIT=${GIT_CURRENT_COMMIT}

RUN mkdir -m 0777 -p /var/tmp/musicloud/files && \
    mkdir -m 0777 -p /var/tmp/musicloud/temp

VOLUME /var/tmp/musicloud
