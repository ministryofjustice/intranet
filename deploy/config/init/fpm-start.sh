#!/bin/sh

if wp core is-installed 2&gt;/dev/null; then
    # WP is installed.
    wp sync-user-roles sync
else
    # Fallback if WP is not installed.
    # This will happen during a first run on localhost.
    echo 'WordPress is not installed yet, so skipping command `wp sync-user-roles sync` in `fpm-start.sh`.'
fi
