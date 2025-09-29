#!/bin/sh

if wp core is-installed 2>/dev/null; then
    # WP is installed.

    # Sync user roles.
    wp sync-user-roles sync
else
    # Fallback if WP is not installed.
    # This will happen during a first run on localhost.
    echo 'WordPress is not installed yet, so skipping command `wp sync-user-roles sync` and `wp cluster-helper register-self` in `fpm-start.sh`.'
fi

# Register the current container/pod with the cluster.
wp cluster-helper register-self --skip-plugins --skip-themes
