#!/usr/bin/env bash

# Please note:
# Sometimes, there is a conflict where `docker compose cp` creates a directory on
# the container when the directory should be a file. This happens with index.php
# files that are referenced as a volume in docker-compose.
#
# The fix is to destroy the container and rebuild it.

docker compose cp php-fpm:/var/www/html/vendor-assets/public ./vendor-assets
docker compose cp ./vendor-assets/public nginx:/var/www/html
