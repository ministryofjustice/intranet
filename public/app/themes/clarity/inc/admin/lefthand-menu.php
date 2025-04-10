<?php

use MOJ\Intranet;

/**
 * Activates the 'menu_order' filter and then hooks into 'menu_order'
 */
add_filter('custom_menu_order', fn() => true);

/**
 * Filters WordPress' default menu order
 */
add_filter('menu_order', function ($menu_order) {
    // The order here is replicated in the admin panel...
    $new_positions = [
        'index.php',
        'admin.php?page=header-settings',
        'admin.php?page=homepage-settings',
        'separator1',
        'edit.php?post_type=note-from-amy',
        'edit.php?post_type=note-from-antonia',
        'edit.php?post_type=news',
        'edit.php?post_type=page',
        'edit.php',
        'edit.php?post_type=event',
        'edit.php?post_type=webchat',
        'edit-comments.php',
        'upload.php',
        'edit.php?post_type=document',
        'edit.php?post_type=regional_pages',
        'edit.php?post_type=regional_news',
        'edit.php?post_type=poll',
        'separator2',
        'themes.php',
        'plugins.php',
        'users.php',
        'tools.php',
        'options-general.php',
        'edit.php?post_type=acf-field-group',
        'elasticpress',
        'edit.php?post_type=team_news',
        'separator-last'
    ];

    // traverse through the new positions and move
    // the items if found in the original menu_positions
    foreach ($new_positions as $new_position => $value) {
        if ($current_index = array_search($value, $menu_order)) {
            $replacement = array_splice($menu_order, $current_index, 1);
            array_splice(
                $menu_order,
                $new_position,
                0,
                $replacement
            );
        }
    }

    return $menu_order;
});

add_action('admin_menu', 'remove_regions_from_nonhmcts_users');

function remove_regions_from_nonhmcts_users()
{
    $context = Agency_Context::get_agency_context();

    if ($context != 'hmcts') {
        remove_menu_page('edit.php?post_type=regional_news');
        remove_menu_page('edit.php?post_type=regional_page');
    }
}

add_action('admin_menu', 'remove_options_from_agency_admin');

function remove_options_from_agency_admin()
{
    // creating functions post_remove for removing menu item
    global $wp_roles;

    $current_user = wp_get_current_user();
    $roles = $current_user->roles;
    $role = array_shift($roles);


    if ($role == 'agency_admin') {
        remove_menu_page('edit.php?post_type=acf-field-group');
        remove_menu_page('options-general.php');
    }
}

add_action('admin_menu', 'remove_options_from_teamusers', 119);
// For newer versions of WP you need to set the priorty quite high to avoid errors. See
// https://codex.wordpress.org/Function_Reference/remove_submenu_page#Notes

function remove_options_from_teamusers()
{
    // creating functions post_remove for removing menu item
    global $wp_roles;

    $current_user = wp_get_current_user();
    $roles = $current_user->roles;
    $role = array_shift($roles);

    if ($role == 'team-author' || $role == 'team-lead') {
        remove_menu_page('edit.php');
        remove_menu_page('edit.php?post_type=acf-field-group');
        remove_menu_page('edit.php?post_type=webchat');
        remove_menu_page('acf-options');
        remove_menu_page('options-general.php');
    }
}

add_action('admin_menu', 'remove_options_from_regionalusers', 120);

function remove_options_from_regionalusers()
{
    // creating functions post_remove for removing menu item
    global $wp_roles;

    $current_user = wp_get_current_user();
    $roles = $current_user->roles;
    $role = array_shift($roles);

    if ($role == 'regional-editor') {
        remove_menu_page('edit.php');
        remove_menu_page('edit.php?post_type=webchat');
        remove_menu_page('edit.php?post_type=news');
    }
}

// General menu item removal across roles
add_action('admin_menu', 'global_remove_menu_items');

function global_remove_menu_items()
{
    if (!current_user_can('administrator')) :
        remove_menu_page('tools.php');
        remove_menu_page('themes.php');
    endif;
}
