#!/bin/sh
set -e

exec /migrate -path /migrations/ -database "postgres://${POSTGRES_USER}:${POSTGRES_PASSWORD}@${POSTGRES_HOST}:5432/${POSTGRES_DB}?sslmode=disable" up
