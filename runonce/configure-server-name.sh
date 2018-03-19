#!/bin/sh

# Configure nginx to pass SERVER_NAME to php-fpm
# Without this, SERVER_NAME is empty due to the nginx server being the default host without a configured server_name
echo "fastcgi_param SERVER_NAME ${SERVER_NAME:='intranet.docker'} ;" > /etc/nginx/server_name.conf

# Give nullmailer a valid default domain
echo "${SERVER_NAME:='intranet.docker'}" > /etc/nullmailer/defaultdomain
