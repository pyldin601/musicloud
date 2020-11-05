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

COPY --from=0 /code/public /code/public
COPY musicloud/nginx/default.conf /etc/nginx/conf.d/
