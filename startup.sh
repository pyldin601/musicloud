#!/usr/bin/env bash

(composer run migrate migrate:status env || composer run migrate migrate:init env) && \
    composer run migrate migrate:up env
