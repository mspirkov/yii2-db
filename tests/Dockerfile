FROM php:8.4-cli-alpine

RUN docker-php-ext-install pdo pdo_mysql
RUN apk update && apk add --no-cache \
    php83-dev \
    autoconf \
    make \
    gcc \
    g++ \
    libc-dev

RUN pecl install pcov
RUN docker-php-ext-enable pcov
