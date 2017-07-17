FROM pldin601/musicloud-image

MAINTAINER Roman Lakhtadyr <roman.lakhtadyr@gmail.com>

RUN apt-get update && \
    apt-get install -y cron && \
    apt-get clean && \
    touch /var/log/cron.log

WORKDIR /usr/app/

COPY composer.json composer.lock ./
RUN composer install --no-plugins --no-scripts --no-dev

COPY package.json package-lock.json ./
RUN npm install

COPY . ./

ARG GIT_CURRENT_COMMIT="<unknown>"
ENV GIT_CURRENT_COMMIT=${GIT_CURRENT_COMMIT}

ARG CRON_ENDPOINT="http://guest:please@localhost:8080/cron"
RUN echo "0 0 5 * * root curl -X POST ${CRON_ENDPOINT}/cleanFileSystem" > /etc/cron.d/cleanFileSystem.cron && \
    echo '* * * * * root curl -X POST ${CRON_ENDPOINT}/generatePeaks' > /etc/cron.d/generatePeaks.cron
