FROM ministryofjustice/wordpress-base-fpm:latest AS base-fpm

###

FROM nginxinc/nginx-unprivileged:1.25-alpine AS base-nginx

USER root

COPY deploy/config/init/* /docker-entrypoint.d/
RUN chmod +x /docker-entrypoint.d/*
RUN echo "# This file is configured at runtime." > /etc/nginx/real_ip.conf

USER 82


## target: dev
FROM base-fpm AS dev
RUN apk add --update nano nodejs npm

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# www-data
USER 82


###


## target: production
FROM base-fpm AS build-fpm-composer

ARG COMPOSER_USER
ARG COMPOSER_PASS

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY ./bin/composer-auth.sh /var/www/html/composer-auth.sh
RUN chmod +x /var/www/html/composer-auth.sh
RUN /var/www/html/composer-auth.sh

# non-root
USER 82

COPY ./composer.json /var/www/html/composer.json
RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .
RUN composer install --no-dev
RUN composer dump-autoload -o && rm -f auth.json

ARG regex_files='\(htm\|html\|js\|css\|png\|jpg\|jpeg\|gif\|ico\|svg\|webmanifest\)'
ARG regex_path='\(app\/themes\/clarity\/error\-pages\|app\/mu\-plugins\|app\/plugins\|wp\)'
RUN mkdir -p ./vendor-assets && \
    find public/ -regex "public\/${regex_path}.*\.${regex_files}" -exec cp --parent "{}" vendor-assets/  \;



###


FROM base-fpm AS build-fpm

WORKDIR /var/www/html
COPY --from=build-fpm-composer --chown=www-data:www-data /var/www/html /var/www/html

# non-root
USER 82

###


FROM build-fpm AS test
RUN make test


###


FROM node:20 AS assets-build

WORKDIR /code
COPY . /code/

WORKDIR /code/public/app/themes/clarity
RUN npm ci
RUN npm run production
RUN rm -rf node_modules


###


FROM base-nginx AS nginx-dev

RUN echo "# This is a placeholder, because the file is included in `php-fpm.conf`." > /etc/nginx/server_name.conf

###


FROM base-nginx AS build-nginx

# Grab server configurations
COPY deploy/config/php-fpm.conf /etc/nginx/php-fpm.conf
COPY deploy/config/server.conf /etc/nginx/conf.d/default.conf

# Grab assets for Nginx
COPY --from=assets-build /code/public/app/themes/clarity/style.css /var/www/html/public/app/themes/clarity/
COPY --from=assets-build /code/public/app/themes/clarity/dist /var/www/html/public/app/themes/clarity/dist/

# Only take what Nginx needs (current configuration)
COPY --from=build-fpm-composer --chown=www-data:www-data /var/www/html/vendor-assets /var/www/html/
COPY --from=build-fpm-composer --chown=www-data:www-data /var/www/html/public/index.php /var/www/html/public/index.php
COPY --from=build-fpm-composer --chown=www-data:www-data /var/www/html/public/wp/wp-admin/index.php /var/www/html/public/wp/wp-admin/index.php
