#!/usr/bin/env bash

# Initialize migration
composer run migrate migrate:init env

# Run pending migrations
composer run migrate migrate:up env
