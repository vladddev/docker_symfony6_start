#!/usr/bin/env bash

/opt/overseer/k8s/scripts/auth.sh

export DOCKER_BUILDKIT=1 # enable buildkit for parallel builds

docker build \
    --pull \
    --target fpm-dev\
    -t nissaya98/main-php:8.1-fpm-alpine-dev \
    .
    .
