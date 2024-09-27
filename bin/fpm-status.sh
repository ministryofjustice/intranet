#!/usr/bin/env sh

# This is a helper script to assist in understanding long running (crashed) fpm processes.

# This script will output details of all running processes e.g:

# pid:                  146880
# state:                Idle
# start time:           27/Sep/2024:09:38:00 +0100
# start since:          6
# requests:             1
# request duration:     1426
# request method:       GET
# request URI:          /app/mu-plugins/moj-auth/401.php
# content length:       0
# user:                 -
# script:               /var/www/html/public/app/mu-plugins/moj-auth/401.php
# last request cpu:     0.00
# last request memory:  2097152

env -i SCRIPT_NAME=/status SCRIPT_FILENAME=/status QUERY_STRING="full&html" REQUEST_METHOD=GET cgi-fcgi -bind -connect /sock/fpm.sock
