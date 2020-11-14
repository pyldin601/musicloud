FROM node:10

WORKDIR /code

COPY package.json /code/package.json
COPY package-lock.json /code/package-lock.json

RUN ["npm", "ci"]

COPY gulpfile.js /code/gulpfile.js
COPY webpack.config.js /code/webpack.config.js

COPY src /code/src
COPY public /code/public

RUN npm run gulp && \
    npm run webpack


FROM nginx:stable-alpine

ARG MAX_UPLOAD_FILESIZE=256
ENV MAX_UPLOAD_FILESIZE=${MAX_UPLOAD_FILESIZE}

COPY --from=0 /code/public /code/public
COPY docker/nginx/default.conf.template /etc/nginx/templates/

EXPOSE 8080
