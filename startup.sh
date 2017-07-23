#!/usr/bin/env bash

# Wait for database ready to handle connections
if [ "$PHP_ENV" = "development" ]; then
    composer install --no-plugins --no-scripts --no-dev
    npm install
    npm run gulp
    npm run webpack
fi

wait-for-it db:5432 -- \
    composer run migrate migrate:init env && \
    composer run migrate migrate:up env
