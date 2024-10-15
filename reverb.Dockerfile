FROM php:8.3-alpine3.20

ARG USER=www-data

WORKDIR /var/www

RUN docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install \
    pcntl

RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis.so

EXPOSE 8880

USER ${USER}

CMD ["php", "artisan", "reverb:start", "--host=0.0.0.0"]
