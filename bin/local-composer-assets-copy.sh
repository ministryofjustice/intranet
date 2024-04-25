#!/usr/bin/env bash

docker compose cp php-fpm:/var/www/html/vendor-assets/public ./vendor-assets
docker compose cp ./vendor-assets/public nginx:/var/www/html
