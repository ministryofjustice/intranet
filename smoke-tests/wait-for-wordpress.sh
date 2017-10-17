#!/bin/sh
set -e

wordpress_host=$1
command_to_run=$2

while ! curl -I -s ${wordpress_host} | grep -q 'HTTP/1.1 200 OK'; do
  >&2 echo "Wordpress is unavailable - sleeping..."
  sleep 10
done

>&2 echo "Wordpress is up - executing command..."

exec ${command_to_run}
