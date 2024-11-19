#!/usr/bin/env ash

# 1️⃣ Send a ping request via fast-cgi. Use an empty environment with `env -i`.
PING_RESPONSE=$(env -i SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET cgi-fcgi -bind -connect /sock/fpm.sock | tail -1 &);

# Store the process ID of the ping request.
PING_PID=$!;

# Wait for 1 second to give the ping request time to complete.
sleep 1;

# Check if the ping request is still running.
if kill -0 $PING_PID 2>/dev/null; then
    # If the ping request is still running, terminate it.
    kill $PING_PID;
    # Print an error message.
    echo "Ping request timed out.";
    # Exit with code 1.
    exit 1;
fi;

# Wait for the ping request process to complete.
wait $PING_PID;

# Exit with code 1 if the ping response was not 'pong'.
if [ "$PING_RESPONSE" != 'pong' ]; then exit 1; fi;



# 2️⃣ Request the fpm status via fast-cgi.
FPM_STATUS=$(env -i SCRIPT_NAME=/status SCRIPT_FILENAME=/status REQUEST_METHOD=GET cgi-fcgi -bind -connect /sock/fpm.sock);

# Store the process ID of the status request.
STATUS_PID=$!;

# Wait for 1 second to give the status request time to complete.
sleep 1;

# Check if the status request is still running.
if kill -0 $STATUS_PID 2>/dev/null; then
    # If the status request is still running, terminate it.
    kill $STATUS_PID;
    # Print an error message.
    echo "Status request timed out.";
    # Exit with code 1.
    exit 1;
fi;

# Wait for the status request process to complete.
wait $STATUS_PID;

# Exit with code 1 if the status is 'Could not connect to /sock/fpm.sock'.
if [ "$(echo "$FPM_STATUS" | grep -c 'Could not connect')" -gt 0 ]; then exit 1; fi;
