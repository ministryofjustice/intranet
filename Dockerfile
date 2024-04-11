FROM ministryofjustice/wordpress-base-fpm:latest AS base-fpm

RUN addgroup -g 101 -S nginx; adduser -u 101 -S -D -G nginx nginx

RUN mkdir /sock && \
    chown nginx:nginx /sock && \
    rm /usr/local/etc/php-fpm.d/zz-docker.conf && \
    rm /usr/local/etc/php-fpm.d/www.conf.default && \
    rm /usr/local/etc/php-fpm.d/www.conf

# Create FPM pool
RUN { \
        echo '[www]'; \
        echo 'user = nginx'; \
        echo 'group = nginx'; \
        echo 'listen = /sock/fpm.sock'; \
        echo 'listen.owner = nginx'; \
        echo 'listen.group = nginx'; \
        echo 'listen.mode = 0660'; \
        echo 'pm = dynamic'; \
        echo 'pm.start_servers = 10'; \
        echo 'pm.min_spare_servers = 5'; \
        echo 'pm.max_spare_servers = 10'; \
        echo 'pm.max_requests = 500'; \
        echo 'pm.max_children = 50'; \
        echo ''; \
        echo '[global]'; \
        echo 'daemonize = no'; \
        echo 'emergency_restart_threshold = 10'; \
        echo 'emergency_restart_interval = 1m'; \
        echo 'process_control_timeout = 10s'; \
    } > /usr/local/etc/php-fpm.d/pool.conf


###


FROM nginxinc/nginx-unprivileged:1.25-alpine AS base-nginx

USER root

COPY deploy/config/init/nginx-* /docker-entrypoint.d/

RUN chmod +x /docker-entrypoint.d/*
RUN echo "# This file is configured at runtime." > /etc/nginx/real_ip.conf

USER 101


## target: dev
FROM base-fpm AS dev

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# nginx
USER 101

VOLUME ["/sock"]


###

## target: production
FROM base-fpm AS build-fpm-composer

ARG COMPOSER_USER
ARG COMPOSER_PASS

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY ./bin/composer-auth.sh /var/www/html/composer-auth.sh
RUN chmod +x /var/www/html/composer-auth.sh && \
    /var/www/html/composer-auth.sh

# non-root
USER 101

COPY composer.json composer.lock /var/www/html/
RUN composer install --no-dev
RUN composer dump-autoload -o

ARG regex_files='\(htm\|html\|js\|css\|png\|jpg\|jpeg\|gif\|ico\|svg\|webmanifest\)'
ARG regex_path='\(app\/themes\/clarity\/error\-pages\|app\/mu\-plugins\|app\/plugins\|wp\)'
RUN mkdir -p ./vendor-assets && \
    find public/ -regex "public\/${regex_path}.*\.${regex_files}" -exec cp --parent "{}" vendor-assets/  \;


###

FROM node:20 AS assets-build

WORKDIR /node
COPY ./public/app/themes/clarity /node/

RUN npm ci
RUN npm run production
RUN rm -rf node_modules


###

FROM base-fpm AS build-fpm

WORKDIR /var/www/html
COPY --chown=nginx:nginx ./config ./config
COPY --chown=nginx:nginx ./public ./public
COPY --from=build-fpm-composer --chown=nginx:nginx /var/www/html/public/app/mu-plugins /var/www/html/public/app/mu-plugins
COPY --from=build-fpm-composer --chown=nginx:nginx /var/www/html/public/app/plugins /var/www/html/public/app/plugins
COPY --from=build-fpm-composer --chown=nginx:nginx /var/www/html/public/app/languages /var/www/html/public/app/languages
COPY --from=build-fpm-composer --chown=nginx:nginx /var/www/html/public/wp /var/www/html/public/wp
COPY --from=build-fpm-composer --chown=nginx:nginx /var/www/html/vendor /var/www/html/vendor

# non-root
USER 101

###

FROM build-fpm AS test
RUN make test


###


FROM base-nginx AS nginx-dev

RUN echo "# This is a placeholder, because the file is included in `php-fpm.conf`." > /etc/nginx/server_name.conf



###

FROM base-nginx AS build-nginx

# Grab server configurations
COPY deploy/config/php-fpm.conf /etc/nginx/php-fpm.conf
COPY deploy/config/server.conf /etc/nginx/conf.d/default.conf

# Get bootstraper for WordPress
COPY public/index.php /var/www/html/public/index.php
COPY public/app/themes/clarity/style.css /var/www/html/public/app/themes/clarity/

# Grab assets for Nginx
COPY --from=assets-build /node/dist /var/www/html/public/app/themes/clarity/dist/

# Only take what Nginx needs (current configuration)
COPY --from=build-fpm-composer --chown=nginx:nginx /var/www/html/public/wp/wp-admin/index.php /var/www/html/public/wp/wp-admin/index.php
COPY --from=build-fpm-composer --chown=nginx:nginx /var/www/html/vendor-assets /var/www/html/


###


FROM alpine:3.19.1 as build-cron

ARG user=crooner
RUN addgroup --gid 3001 ${user} && adduser -D -G ${user} -g "${user} user" -u 3001 ${user}

RUN apk add dpkg curl tzdata

RUN ln -s /usr/share/zoneinfo/Europe/London /etc/localtime

## cron-schedule directory
RUN mkdir -p /schedule && chown ${user}:${user} /schedule

COPY deploy/config/cron/wp-cron /schedule/wp-cron
COPY deploy/config/cron/wp-cron-exec.sh /usr/bin/wp-cron-exec
COPY deploy/config/init/cron-install.sh /usr/bin/cron-install
COPY deploy/config/init/cron-start.sh /usr/bin/cron-start

RUN chmod +x /usr/bin/wp-cron-exec && \
    chmod +x /usr/bin/cron-install && \
    chmod +x /usr/bin/cron-start

RUN cron-install

RUN apk del dpkg

USER 3001

ENTRYPOINT ["/bin/sh", "-c", "cron-start"]


