FROM php:8.5-cli-alpine

RUN docker-php-ext-install pdo pdo_mysql
RUN apk add --no-cache $PHPIZE_DEPS linux-headers

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

ENV XDEBUG_MODE=coverage
