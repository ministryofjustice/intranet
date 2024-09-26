#!/usr/bin/env ash

# Send a ping request via fcgi
PING_RESPONSE=$(SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET cgi-fcgi -bind -connect /sock/fpm.sock | tail -1);

# Exit with code 1 if the ping response was not 'pong'.
if [ $(echo $PING_RESPONSE) != 'pong' ]; then exit 1; fi;

# Request the fpm status via fcgi
FPM_STATUS=$(SCRIPT_NAME=/status SCRIPT_FILENAME=/status REQUEST_METHOD=GET cgi-fcgi -bind -connect /sock/fpm.sock);

# Exit with code 1 if the status is 'Could not connect to /sock/fpm.sock'.
if [ $(echo "$FPM_STATUS" | grep -c 'Could not connect') -gt 0 ]; then exit 1; fi;
