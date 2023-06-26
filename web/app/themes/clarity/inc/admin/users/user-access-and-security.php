<?php
/**
 * Tracks when a user logs into WP
 * Used in admin area to display last logged in, in the account and permissions dashboard.
 */

add_action('set_current_user', function() {
    global $current_user;
    add_user_meta($current_user->ID, 'user_login_record', current_time('mysql'));
}, 10);
