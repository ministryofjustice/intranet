<?php

/**
 * Tracks when a user logs into WP
 * Used in admin area to display last logged in, in the account and permissions dashboad.
 */
add_action('set_current_user', 'clarity_set_user_login_time', 10, 2);

function clarity_set_user_login_time()
{

    global $current_user;

    add_user_meta($current_user->ID, 'user_login_record', current_time('mysql'));
}
