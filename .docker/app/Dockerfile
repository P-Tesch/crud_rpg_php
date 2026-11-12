FROM node:26-alpine3.24 AS node

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


FROM dunglas/frankenphp:php8.5-alpine

ARG USER=www-data

COPY .docker/app/Caddyfile /etc/caddy/Caddyfile

COPY . /var/www
COPY --from=node /app/public/build /var/www/public/build
COPY --from=node /app/node_modules /var/www/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

WORKDIR /var/www

RUN apk update \
    && apk add --update \
    postgresql-dev \
    curl \
    curl-dev \
    libxml2-dev \
    libzip-dev \
    linux-headers \
    libstdc++ \
    libgcc

RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis.so

RUN docker-php-ext-install \
    intl \
    pcntl \
    zip \
    pdo_pgsql \
    pgsql \
    sockets

RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
	&& chown -R ${USER}:${USER} /data/caddy && chown -R ${USER}:${USER} /config/caddy \
    && chown -R ${USER}:${USER} /var/www/storage

RUN install-php-extensions @composer \
    && composer install --no-dev --no-interaction \
    && php artisan key:generate \
    && php artisan storage:link --force \
    && php artisan optimize

RUN rm -rf /var/cache/apk/* /tmp/* /var/tmp/*

USER ${USER}

ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--workers=2", "--watch"]
