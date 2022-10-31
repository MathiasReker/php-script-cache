FROM composer:latest AS composer

FROM php:8.1-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

LABEL org.opencontainers.image.description="php-script-cache is a PHP library for caching external script locally."

WORKDIR /app
COPY . .

RUN apt-get update \
    && apt-get -y upgrade \
    && apt-get -y install zip \
    && composer update
