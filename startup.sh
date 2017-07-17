#!/usr/bin/env bash

sleep 5

cron

wait-for-it db:5432 -- \
    composer run migrate migrate:init env && \
    composer run migrate migrate:up env
