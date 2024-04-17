#!/bin/sh

## $1 ($NGINX_SERVICE_PORT) is available in the container
## Replace 'tcp' with 'http'
NGINX_HTTP_URL=$(echo "$NGINX_SERVICE_PORT" | sed 's/tcp/http/');

wget_it() {
  wget --spider --quiet "$NGINX_HTTP_URL/wp/wp-cron.php"
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

# shellcheck disable=SC2039
HOST_TEST=$(contains "$HOSTNAME" "-prod")

if [ "$HOST_TEST" = 0 ]; then
  wget_it
else
  NOW=$(date +"%H")
  if [ "$NOW" -gt "6" ] && [ "$NOW" -lt "22" ]; then
    wget_it
  fi
fi
