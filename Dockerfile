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

FROM ministryofjustice/wordpress-base-fpm:0.0.7@sha256:397aa5f03e34a3c16a7f620a5518b7d4bf40ba8ede3fa11c0bf62d12e6ea0d18 AS base-fpm

# Switch to the alpine's default user, for installing packages
USER root

RUN apk update && \
    apk add strace

# Make the Nginx user available in this container
RUN addgroup -g 101 -S nginx; adduser -u 101 -S -D -G nginx nginx

RUN mkdir /sock && \
    chown nginx:nginx /sock

# Copy our init. script(s) and set them to executable
COPY bin/fpm-init.sh /usr/local/bin/

RUN chmod +x /usr/local/bin/fpm-init.sh

# Copy our healthcheck scripts and set them to executable
COPY bin/fpm-liveness.sh bin/fpm-readiness.sh bin/fpm-status.sh /usr/local/bin/fpm-health/

RUN chmod +x /usr/local/bin/fpm-health/*

## Change directory
WORKDIR /usr/local/etc/php-fpm.d

## Clean PHP pools; leave docker.conf in situe
RUN rm zz-docker.conf && \
    rm www.conf.default && \
    rm www.conf

## Set our pool configuration
COPY deploy/config/php-pool.conf pool.conf    

# Don't log every request.
RUN perl -pi -e 's#^(?=access\.log\b)#;#' /usr/local/etc/php-fpm.d/docker.conf

WORKDIR /var/www/html


#    ▄▄  ▄▄     █▄░█  █▀▀  █  █▄░█  ▀▄▀     ▄▄  ▄▄    #
#    ░░  ░░     █░▀█  █▄█  █  █░▀█  █░█     ░░  ░░    #

# Use --platform=linux/amd64 flag and match version numbers to ensure module and runtime compatibility.
FROM --platform=linux/amd64  nginx:1.29.5-alpine@sha256:1eff5a5f3fcf8431a0abb7eddf5471fec24e5e1905a2581aeacdb07a4479b92b AS nginx-module-builder

SHELL ["/bin/ash", "-exo", "pipefail", "-c"]

RUN apk update && \
    apk add linux-headers openssl-dev pcre2-dev zlib-dev openssl abuild \
        musl-dev libxslt libxml2-utils make gcc unzip git \
        xz g++ coreutils

RUN printf "#!/bin/sh\\nSETFATTR=true /usr/bin/abuild -F \"\$@\"\\n" > /usr/local/bin/abuild && \
    chmod +x /usr/local/bin/abuild && \
    git clone --branch ${NGINX_VERSION}-${PKG_RELEASE} https://github.com/nginx/pkg-oss.git pkg-oss && \
    mkdir -p /tmp/packages && \
    cd pkg-oss && \
    /pkg-oss/build_module.sh -v $NGINX_VERSION -f -y -o /tmp/packages -n cachepurge https://github.com/nginx-modules/ngx_cache_purge/archive/2.5.6.tar.gz; \
    BUILT_MODULES="$BUILT_MODULES $(echo cachepurge | tr '[A-Z]' '[a-z]' | tr -d '[/_\-\.\t ]')"; \
    cd /tmp && ls -l; \
    echo "BUILT_MODULES=\"$BUILT_MODULES\"" > /tmp/packages/modules.env; \
    cd packages && ls -l

# Use --platform=linux/amd64 flag and match version numbers to ensure module and runtime compatibility.
FROM --platform=linux/amd64 nginxinc/nginx-unprivileged:1.29.5-alpine@sha256:be85b7aa36f49ad5ca46add107870356adce48a82819178f30151132e1cc0641 AS base-nginx

USER root

RUN --mount=type=bind,target=/tmp/packages/,source=/tmp/packages/,from=nginx-module-builder \
    . /tmp/packages/modules.env \
    &&  apk add --no-cache --allow-untrusted /tmp/packages/nginx-module-cachepurge-${NGINX_VERSION}*.apk;

RUN mkdir /var/run/nginx-cache && \
    chown nginx:nginx /var/run/nginx-cache

# contains gzip and module include
COPY --chown=nginx:nginx deploy/config/nginx.conf /etc/nginx/nginx.conf

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

WORKDIR /var/www/html

ARG ACF_PRO_LICENSE
ARG ACF_PRO_PASS
ARG AS3CF_PRO_USER
ARG AS3CF_PRO_PASS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY ./bin/composer-auth.sh ./bin/composer-post-install.sh ./bin/

RUN chmod +x ./bin/composer-auth.sh && \
    ./bin/composer-auth.sh
RUN chmod +x ./bin/composer-post-install.sh

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


FROM node:25-alpine3.23@sha256:5209bcaca9836eb3448b650396213dbe9d9a34d31840c2ae1f206cb2986a8543 AS assets-build

WORKDIR /node
COPY ./public/app/themes/clarity /node/

RUN npm ci
RUN npm run production
RUN rm -rf node_modules


#  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

#  █▀▀  █▀█  █▀▄▀█
#  █▀░  █▀▀  █░▀░█


FROM base-fpm AS build-fpm

# Set the WP_CLI configuration path - so that the `wp` command can be run from anywhere 
# e.g. /usr/local/bin/docker-entrypoint.d/fpm-start.sh
ENV WP_CLI_CONFIG_PATH=/var/www/html/wp-cli.yml

WORKDIR /var/www/html
COPY --chown=nginx:nginx ./config ./config
COPY --chown=nginx:nginx ./public ./public
COPY --chown=nginx:nginx wp-cli.yml wp-cli.yml

# Replace paths with dependencies from build-fpm-composer
ARG path="/var/www/html"
COPY --from=build-fpm-composer ${path}/public/app/mu-plugins public/app/mu-plugins
COPY --from=build-fpm-composer ${path}/public/app/plugins public/app/plugins
COPY --from=build-fpm-composer ${path}/public/app/languages public/app/languages
COPY --from=build-fpm-composer ${path}/public/wp public/wp
COPY --from=build-fpm-composer ${path}/vendor vendor

# non-root
USER 101

# Set IMAGE_TAG at build time, we don't want this container to be run with an incorrect IMAGE_TAG.
# Set towards the end of the Dockerfile to benefit from caching.
ARG IMAGE_TAG
ENV IMAGE_TAG=$IMAGE_TAG


#  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

#  █▄░█  █▀▀  █  █▄░█  ▀▄▀
#  █░▀█  █▄█  █  █░▀█  █░█


FROM base-nginx AS build-nginx

# Grab server configurations
COPY deploy/config/php-fpm.conf      /etc/nginx/php-fpm.conf
COPY deploy/config/php-fpm-auth.conf /etc/nginx/php-fpm-auth.conf
COPY deploy/config/auth-request.conf /etc/nginx/auth-request.conf
COPY deploy/config/server.conf       /etc/nginx/conf.d/default.conf

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


FROM alpine:3.23@sha256:25109184c71bdad752c8312a8623239686a9a2071e8825f20acb8f2198c3f659 AS build-cron

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


#  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

#  █▀█ █░█ █▀ █░█ █▀▀ █▀█
#  █▀▀ █▄█ ▄█ █▀█ ██▄ █▀▄


FROM alpine:3.23@sha256:25109184c71bdad752c8312a8623239686a9a2071e8825f20acb8f2198c3f659 AS build-s3-push

ARG user=s3pusher
RUN addgroup --gid 3001 ${user} && adduser -D -G ${user} -g "${user} user" -u 3001 ${user}

RUN apk add --no-cache aws-cli jq

WORKDIR /usr/bin

COPY deploy/config/init/s3-push-start.sh ./s3-push-start
RUN chmod +x s3-push-start

USER 3001

# Go home...
WORKDIR /home/s3pusher

# Create .aws directory for AWS CLI configuration and a tmp directory for other temp files.
# This will be the only writable location in the read-only container.
RUN mkdir -p .aws && mkdir -p tmp

# Grab assets for pushing to s3
COPY --from=build-fpm-composer /var/www/html/vendor-assets ./
COPY --from=assets-build /node/dist public/app/themes/clarity/dist/

# Set IMAGE_TAG at build time, we don't want this container to be run with an incorrect IMAGE_TAG.
# Set towards the end of the Dockerfile to benefit from caching.
ARG IMAGE_TAG
ENV IMAGE_TAG=$IMAGE_TAG

ENTRYPOINT ["/bin/sh", "-c", "s3-push-start"]
