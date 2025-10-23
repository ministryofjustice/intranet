#!/bin/sh

if wp core is-installed 2>/dev/null; then
    # WP is installed.

    # Sync user roles.
    wp sync-user-roles sync

    # Remove scheduled events for: wp_version_check, wp_update_plugins and wp_update_themes
    wp cron event delete wp_version_check
    wp cron event delete wp_update_plugins
    wp cron event delete wp_update_themes

    # Remove scheduled events for TotalPoll plugin that are not needed.
    wp cron event delete totalpoll_weekly_environment
    wp cron event delete totalpoll_daily_activity
else
    # Fallback if WP is not installed.
    # This will happen during a first run on localhost.
    echo 'WordPress is not installed yet, so skipping command `wp sync-user-roles sync` in `fpm-start.sh`.'
fi
