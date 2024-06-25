#░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░
#░░
#░░     ▒█▀▄▀█ █▀▀█ ░░░▒█ 　 ▀█▀ █▀▀▄ ▀▀█▀▀ █▀▀█ █▀▀█ █▀▀▄ █▀▀ ▀▀█▀▀
#░░     ▒█▒█▒█ █░░█ ░▄░▒█ 　 ▒█░ █░░█ ░░█░░ █▄▄▀ █▄▄█ █░░█ █▀▀ ░░█░░
#░░     ▒█░░▒█ ▀▀▀▀ ▒█▄▄█ 　 ▄█▄ ▀░░▀ ░░▀░░ ▀░▀▀ ▀░░▀ ▀░░▀ ▀▀▀ ░░▀░░
#░░
#░░     ▀█▀ █▀▄▀█ █▀▀█ █▀▀▀ █▀▀ 　 ▒█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░▀░ █▀▀▀
#░░     ▒█░ █░▀░█ █▄▄█ █░▀█ █▀▀ 　 ▒█░░░ █░░█ █░░█ █▀▀ ▀█▀ █░▀█
#░░     ▄█▄ ▀░░░▀ ▀░░▀ ▀▀▀▀ ▀▀▀ 　 ▒█▄▄█ ▀▀▀▀ ▀░░▀ ▀░░ ▀▀▀ ▀▀▀▀
#░░
#░░    (¯`v´¯)
#░░     `.¸.[Code]
#░░
#░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░


#    ▄▄  ▄▄     █▀▀  █▀█  █▀▄▀█     ▄▄  ▄▄    #
#    ░░  ░░     █▀░  █▀▀  █░▀░█     ░░  ░░    #

FROM ministryofjustice/wordpress-base-fpm:latest AS base-fpm

# Make the Nginx user available in this container
RUN addgroup -g 101 -S nginx; adduser -u 101 -S -D -G nginx nginx

RUN mkdir /sock && \
    chown nginx:nginx /sock

## Change directory
WORKDIR /usr/local/etc/php-fpm.d

## Clean PHP pools; leave docker.conf in situe
RUN rm zz-docker.conf && \
    rm www.conf.default && \
    rm www.conf

## Set our pool configuration
COPY deploy/config/php-pool.conf pool.conf


#    ▄▄  ▄▄     █▄░█  █▀▀  █  █▄░█  ▀▄▀     ▄▄  ▄▄    #
#    ░░  ░░     █░▀█  █▄█  █  █░▀█  █░█     ░░  ░░    #

FROM nginxinc/nginx-unprivileged:1.25-alpine AS base-nginx

USER root

COPY deploy/config/init/nginx-* /docker-entrypoint.d/

RUN chmod +x /docker-entrypoint.d/*; \
    echo "# This file is configured at runtime." > /etc/nginx/real_ip.conf

USER 101




#
#   ▒█▀▀▄ █▀▀ ▀█░█▀ █▀▀ █░░ █▀▀█ █▀▀█ █▀▄▀█ █▀▀ █▀▀▄ ▀▀█▀▀
#   ▒█░▒█ █▀▀ ░█▄█░ █▀▀ █░░ █░░█ █░░█ █░▀░█ █▀▀ █░░█ ░░█░░
#   ▒█▄▄▀ ▀▀▀ ░░▀░░ ▀▀▀ ▀▀▀ ▀▀▀▀ █▀▀▀ ▀░░░▀ ▀▀▀ ▀░░▀ ░░▀░░
#
#   ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

#   █▀▀  █▀█  █▀▄▀█
#   █▀░  █▀▀  █░▀░█

FROM base-fpm AS fpm-dev

RUN apk add zip

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

VOLUME ["/sock"]
# nginx
USER 101


#  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

#  █▄░█  █▀▀  █  █▄░█  ▀▄▀
#  █░▀█  █▄█  █  █░▀█  █░█

FROM base-nginx AS nginx-dev

RUN echo "# This is a placeholder because the file is included in php-fpm.conf." > /etc/nginx/server_name.conf


#  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

#  ▀█▀  █▀▀  █▀  ▀█▀
#  ░█░  ██▄  ▄█  ░█░

FROM build-fpm AS test
RUN make test




#
#   ▒█▀▀█ █▀▀█ █▀▀█ █▀▀▄ █░░█ █▀▀ ▀▀█▀▀ ░▀░ █▀▀█ █▀▀▄
#   ▒█▄▄█ █▄▄▀ █░░█ █░░█ █░░█ █░░ ░░█░░ ▀█▀ █░░█ █░░█
#   ▒█░░░ ▀░▀▀ ▀▀▀▀ ▀▀▀░ ░▀▀▀ ▀▀▀ ░░▀░░ ▀▀▀ ▀▀▀▀ ▀░░▀
#
#   ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

#   █▀▀  █▀█  █▀▄▀█  █▀█  █▀█  █▀  █▀▀  █▀█
#   █▄▄  █▄█  █░▀░█  █▀▀  █▄█  ▄█  ██▄  █▀▄


FROM base-fpm AS build-fpm-composer

ARG COMPOSER_USER
ARG COMPOSER_PASS
ARG AS3CF_PRO_USER
ARG AS3CF_PRO_PASS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY ./bin/composer-auth.sh composer-auth.sh
RUN chmod +x composer-auth.sh && \
    ./composer-auth.sh

USER 101

COPY composer.json composer.lock /var/www/html/
RUN composer install --no-dev
RUN composer dump-autoload -o

ARG regex_files='\(htm\|html\|js\|css\|png\|jpg\|jpeg\|gif\|ico\|svg\|webmanifest\)'
ARG regex_path='\(app\/mu\-plugins\|app\/plugins\|wp\)'
RUN mkdir -p ./vendor-assets && \
    find public/ -regex "public\/${regex_path}.*\.${regex_files}" -exec cp --parent "{}" vendor-assets/  \;


#  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

#  ▄▀█  █▀  █▀  █▀▀  ▀█▀  █▀
#  █▀█  ▄█  ▄█  ██▄  ░█░  ▄█


FROM node:20 AS assets-build

WORKDIR /node
COPY ./public/app/themes/clarity /node/

RUN npm ci
RUN npm run production
RUN rm -rf node_modules


#  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

#  █▀▀  █▀█  █▀▄▀█
#  █▀░  █▀▀  █░▀░█


FROM base-fpm AS build-fpm

WORKDIR /var/www/html
COPY --chown=nginx:nginx ./config ./config
COPY --chown=nginx:nginx ./public ./public
COPY --chown=nginx:nginx wp-cli.yml wp-cli.yml

# Replace paths with dependanies from build-fpm-composer
ARG path="/var/www/html"
COPY --from=build-fpm-composer ${path}/public/app/mu-plugins public/app/mu-plugins
COPY --from=build-fpm-composer ${path}/public/app/plugins public/app/plugins
COPY --from=build-fpm-composer ${path}/public/app/languages public/app/languages
COPY --from=build-fpm-composer ${path}/public/wp public/wp
COPY --from=build-fpm-composer ${path}/vendor vendor

# non-root
USER 101


#  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

#  █▄░█  █▀▀  █  █▄░█  ▀▄▀
#  █░▀█  █▄█  █  █░▀█  █░█


FROM base-nginx AS build-nginx

# Grab server configurations
COPY deploy/config/php-fpm.conf /etc/nginx/php-fpm.conf
COPY deploy/config/server.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

# Get bootstraper for WordPress
COPY public/index.php public/index.php

# Only take what Nginx needs (cached configuration)
COPY --from=build-fpm-composer /var/www/html/public/wp/wp-admin/index.php public/wp/wp-admin/index.php
COPY --from=build-fpm-composer /var/www/html/vendor-assets ./

# Grab assets for Nginx
COPY --from=assets-build --chown=nginx:nginx /node/dist public/app/themes/clarity/dist/
COPY --from=assets-build --chown=nginx:nginx /node/error-pages public/app/themes/clarity/error-pages/
COPY --from=assets-build --chown=nginx:nginx /node/style.css public/app/themes/clarity/style.css


#  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

#  █▀▀  █▀█  █▀█  █▄░█
#  █▄▄  █▀▄  █▄█  █░▀█


FROM alpine:3.19.1 as build-cron

#  ▒█▀▀█ █▀▀█ █▀▀█ █▀▀█ █▀▀▄ █▀▀ █▀▀█ 　 █
#  ▒█░░░ █▄▄▀ █░░█ █░░█ █░░█ █▀▀ █▄▄▀ 　 ▀
#  ▒█▄▄█ ▀░▀▀ ▀▀▀▀ ▀▀▀▀ ▀░░▀ ▀▀▀ ▀░▀▀ 　 ▄
#  𝕋𝕙𝕖 𝕊𝕞𝕠𝕠𝕥𝕙 ℕ𝕠𝕟-ℝ𝕠𝕠𝕥 𝕌𝕤𝕖𝕣

ARG user=crooner
RUN addgroup --gid 3001 ${user} && adduser -D -G ${user} -g "${user} user" -u 3001 ${user}

RUN apk add dpkg tzdata && \
    ln -s /usr/share/zoneinfo/Europe/London /etc/localtime

## cron-schedule directory
RUN mkdir -p /schedule && chown ${user}:${user} /schedule

COPY deploy/config/cron/wp-cron /schedule/wp-cron

# Change directory for the rest
WORKDIR /usr/bin

COPY deploy/config/cron/wp-cron-exec.sh ./execute-wp-cron
COPY deploy/config/init/cron-install.sh ./cron-install
COPY deploy/config/init/cron-start.sh ./cron-start

RUN chmod +x execute-wp-cron cron-install cron-start && \
    cron-install &&  \
    rm ./cron-install

RUN apk del dpkg

USER 3001

# Go home...
WORKDIR /home/crooner

ENTRYPOINT ["/bin/sh", "-c", "cron-start"]
