FROM pldin601/musicloud-image

MAINTAINER Roman Lakhtadyr <roman.lakhtadyr@gmail.com>

# Install cron
RUN apt-get update && \
    apt-get install -y cron && \
    apt-get clean && \

    { \
        echo; \
        echo "[program:cron]"; \
        echo "command=cron -f"; \
    } | tee -a /etc/supervisor/supervisord.conf

WORKDIR /usr/app/

COPY composer.json composer.lock ./
RUN composer install --no-plugins --no-scripts --no-dev

COPY package.json package-lock.json ./
RUN npm install

COPY . ./

ARG GIT_CURRENT_COMMIT="<unknown>"
ENV GIT_CURRENT_COMMIT=${GIT_CURRENT_COMMIT}

COPY cronfile /etc/cron.d/musicloud.cronfile
RUN chmod 0644 /etc/cron.d/musicloud-cronfile
