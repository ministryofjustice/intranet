#!/bin/sh

# set hosts
ENV_HOST=$1    # raw hostname of the container
NGINX_HOST=$2  # hostname of the nginx service

curl_it() {
  curl http://"$NGINX_HOST":8080/wp/wp-cron.php --silent
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
  curl_it
else
  NOW=$(date +"%H")
  if [ "$NOW" -gt "6" ] && [ "$NOW" -lt "22" ]; then
    curl_it
  fi
fi
