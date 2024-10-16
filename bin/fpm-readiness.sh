#!/usr/bin/env ash

# Send a ping request via fast-cgi. Use an empty environment with `env -i`.
PING_RESPONSE=$(env -i SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET cgi-fcgi -bind -connect /sock/fpm.sock | tail -1);

# Exit with code 1 if the ping response was not 'pong'.
if [ "$PING_RESPONSE" != 'pong' ]; then exit 1; fi;

# Request the fpm status via fast-cgi.
FPM_STATUS=$(env -i SCRIPT_NAME=/status SCRIPT_FILENAME=/status REQUEST_METHOD=GET cgi-fcgi -bind -connect /sock/fpm.sock);

# Exit with code 1 if the status is 'Could not connect to /sock/fpm.sock'.
if [ "$(echo "$FPM_STATUS" | grep -c 'Could not connect')" -gt 0 ]; then exit 1; fi;
