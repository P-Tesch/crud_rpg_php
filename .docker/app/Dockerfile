# Frontend assets are built on the official Node image (newest stable release line)
FROM node:26-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


FROM dunglas/frankenphp:php8.5-alpine

ARG USER=www-data

COPY .docker/app/Caddyfile /etc/caddy/Caddyfile

COPY . /var/www
COPY --from=assets /app/public/build /var/www/public/build

WORKDIR /var/www

RUN apk update \
    && apk add --update \
    postgresql-dev \
    curl \
    curl-dev \
    libxml2-dev \
    libzip-dev

RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis.so

RUN docker-php-ext-install \
    intl \
    pcntl \
    zip \
    pdo_pgsql \
    pgsql

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
