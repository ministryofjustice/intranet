#!/bin/sh

# Check if this is a job pod (job-marker directory exists)
if [ ! -d "/job-marker" ]; then
    exit 0
fi

# If we are here then we are in a container that's been started by the FPM init Job.
# Note: the deployment does not wait for this job to complete before starting other pods.
# so the content of this script should be designed accordingly.

if wp core is-installed 2>/dev/null; then
    # WP is installed.

    # Sync user roles.
    wp sync-user-roles sync
else
    # Fallback if WP is not installed.
    # This will happen during a first run on localhost.
    echo 'WordPress is not installed yet, so skipping command `wp sync-user-roles sync` in `fpm-start.sh`.'
fi
