#!/bin/sh

# Function to delete a cron event if it exists
delete_cron_if_exists() {
    local event_name="$1"
    
    if [ -z "$event_name" ]; then
        echo "Error: No event name provided to delete_cron_if_exists"
        return 1
    fi
    
    # Check if the cron event exists
    if wp cron event list --format=csv --fields=hook --skip-plugins --skip-themes | grep -q "^$event_name$"; then
        echo "Deleting cron event: $event_name"
        wp cron event delete "$event_name" --skip-plugins --skip-themes
    else
        echo "Cron event '$event_name' does not exist, skipping deletion"
    fi
}

if wp core is-installed 2>/dev/null; then
    # WP is installed.

    # Sync user roles.
    wp sync-user-roles sync --skip-plugins

    # Remove scheduled events for: wp_version_check, wp_update_plugins and wp_update_themes
    delete_cron_if_exists wp_version_check
    delete_cron_if_exists wp_update_plugins
    delete_cron_if_exists wp_update_themes

    # Remove scheduled events for TotalPoll plugin that are not needed.
    delete_cron_if_exists totalpoll_weekly_environment
    delete_cron_if_exists totalpoll_daily_activity
else
    # Fallback if WP is not installed.
    # This will happen during a first run on localhost.
    echo 'WordPress is not installed yet, so skipping command `wp sync-user-roles sync` in `fpm-start.sh`.'
fi
