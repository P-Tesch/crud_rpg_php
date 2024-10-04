FROM dunglas/frankenphp:php8.3-alpine

ARG USER=www-data

COPY . /var/www

WORKDIR /var/www

RUN apk update \
    && apk add --update \
    postgresql-dev \
    nodejs npm \
    curl \
    curl-dev \
    libxml2-dev \
    libzip-dev

RUN docker-php-ext-install \
    intl \
    pcntl \
    zip \
    pdo_pgsql \
    pgsql

RUN npm install \
    && npm run build

RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
	&& chown -R ${USER}:${USER} /data/caddy && chown -R ${USER}:${USER} /config/caddy \
    && chown -R ${USER}:${USER} /var/www/storage

RUN install-php-extensions @composer \
    && composer install --no-dev --no-interaction \
    && php artisan key:generate \
    && php artisan storage:link \
    && php artisan optimize

RUN rm -rf /var/cache/apk/* /tmp/* /var/tmp/*

USER ${USER}

ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--workers=2"]
