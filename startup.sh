#!/usr/bin/env bash

# If started in development env install all dependencies
if [ "$PHP_ENV" = "development" ]; then
    make local-install local-build
fi

# Wait for database ready to handle connections
wait-for-it db:5432 -- \
    composer run migrate migrate:init env && \
    composer run migrate migrate:up env
