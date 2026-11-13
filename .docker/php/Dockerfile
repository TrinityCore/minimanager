FROM php:8.3-fpm-alpine
RUN apk add --no-cache gmp-dev \
    && docker-php-ext-install mysqli gmp
