############################################
# Base Image
############################################
FROM serversideup/php:8.3-fpm-nginx-alpine AS base

############################################
# Development Image
############################################
FROM base AS development

USER root

# Install PHP extensions
RUN install-php-extensions intl

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_22.x -o nodesource_setup.sh \
    && bash nodesource_setup.sh \
    && apt-get install -y nodejs \
    && rm nodesource_setup.sh

ARG USER_ID
ARG GROUP_ID

RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID && \
    \
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service nginx

USER www-data

RUN composer install

RUN php artisan migrate

RUN php artisan key:generate

RUN php artisan storage:link

RUN php artisan db:seed

RUN npm install

RUN npm run build

RUN npm run dev

