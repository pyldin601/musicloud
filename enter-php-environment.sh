#!/bin/bash
set -e

docker build -t musicloud-php-environment --build-arg USER="$(id -u):$(id -g)" -f docker/dev/php-environment/Dockerfile .

exec docker run --rm -it --name musicloud-php-environment --user "$(id -u)" -v "$(pwd):"/code musicloud-php-environment bash
