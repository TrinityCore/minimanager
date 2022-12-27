FROM php:8.0-fpm-alpine
RUN apk update \
    apk upgrade;
RUN apk add --upgrade php8-gmp gmp-dev;

RUN docker-php-ext-install mysqli gmp;

