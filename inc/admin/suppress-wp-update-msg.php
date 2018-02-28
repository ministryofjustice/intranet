<?php

/**
 * WP version notice is useless to editors so I've removed it so as to declutter their WP screen.
 *
 */
// Exit if accessed directly
if (! defined('ABSPATH')) {
    die();
}

add_action( 'admin_head', 'hide_update_notice_to_all_but_admin_users', 1 );

function hide_update_notice_to_all_but_admin_users()
{
    if (!current_user_can('update_core')) {
        remove_action( 'admin_notices', 'update_nag', 3 );
    }
}
