<?php

/**
 * This section modifies the TotalPoll plugin in admin to suit our theme
 * For frontend polls code see src/components/c-polls
 */

// Check if plugin is activated
if (in_array('totalpoll-lite/totalpoll.php', apply_filters('active_plugins', get_option('active_plugins')))) :

    /**
     * BUG fix we have to Load Tiny MCE editor as this plugin needs it and throws a JS error without it.
     */

    add_action('admin_enqueue_scripts', 'load_tinymce');

    function load_tinymce()
    {
        wp_enqueue_script('tinymce', 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.5.6/tinymce.min.js');
    }

    /**
     * Remove (for editors) options and settings not needed.
     */
    add_action('do_meta_boxes', 'remove_poll_meta_boxes_and_tabs');

    // Remove everything that an editor doesn't need to see.
    function remove_poll_meta_boxes_and_tabs()
    {

        if (! current_user_can('administrator')) {
            echo '<style> .settings-tabs, .containables-types li:nth-child(1n+3) { display:none !important } a[data-tp-tab="browse-submissions"] { display:none !important }</style>';
            echo '<style> .settings-tabs, .settings-item:nth-child(1n+3) { display:none !important }</style>';
            remove_meta_box('postimagediv', 'poll', 'side');
            remove_meta_box('postexcerpt', 'poll', 'normal');
        }
    }

    // Remove lefthand menu items
    add_action('admin_menu', 'remove_menu_links', 9999);

    function remove_menu_links()
    {
        if (! current_user_can('administrator')) {
            remove_submenu_page('edit.php?post_type=poll', 'tp-about');
            remove_submenu_page('edit.php?post_type=poll', 'tp-support');
        }
    }

else :
    trigger_error("Hey! TotalPolls plugin has been deactivated and I'm using it (inc/polls.php).", E_USER_NOTICE);
endif;
