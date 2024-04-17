#!/bin/sh

## $NGINX_SERVICE_PORT is available in the container
## Replace 'tcp' with 'http'
NGINX_HOST=$(echo "$NGINX_SERVICE_PORT" | sed 's/tcp/http/');

wget_it() {
  wget --spider --quiet http://"$NGINX_HOST"/wp/wp-cron.php
}

contains() {
    string="$1"
    substring="$2"
    if [ "${string#*"$substring"}" != "$string" ]; then
        return 0    # in $string
    else
        return 1    # not in $string
    fi
}

HOST_TEST=$(contains "$ENV_HOST" "-prod")

if [ "$HOST_TEST" = 0 ]; then
  wget_it
else
  NOW=$(date +"%H")
  if [ "$NOW" -gt "6" ] && [ "$NOW" -lt "22" ]; then
    wget_it
  fi
fi
