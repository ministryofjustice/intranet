<?php

/**
 * WP Admin Bar modifications
 */


/**
 * Initialise WP admin Toolbar
 * https://codex.wordpress.org/Toolbar
 * Also on parent theme but required here for toolbar to show up on new theme.
 */
add_action('init', '_wp_admin_bar_init');

/**
 * Agency Context Switcher
 */
add_action('admin_bar_menu', 'agency_context_switcher_menu', 99);

function agency_context_switcher_menu($wp_admin_bar)
{

    if (! Agency_Context::current_user_can_have_context()) {
        return false;
    }

    $context = Agency_Context::get_agency_context();
    $agency  = Agency_Editor::get_agency_by_slug($context);

    global $wp_roles;

    $current_user = wp_get_current_user();
    $roles        = $current_user->roles;
    $role         = array_shift($roles);

    $agency_role = isset($agency->name) ? $agency->name : 'Team account';

    if (current_user_can('administrator')) {
        $wp_admin_bar->add_node(
            array(
                'parent' => 'top-secondary',
                'id'     => 'agency-context-switcher',
                'title'  => 'Current agency: ' . $agency_role,
                'href'   => '#',
                'meta'   => [
                    'title' => 'Click to switch agency'
                ]
            )
        );
        
        $agencies = Agency_Context::current_user_available_agencies();
        
        if (count($agencies) > 1) {
            $wp_admin_bar->add_group(
                array(
                    'parent' => 'agency-context-switcher',
                    'id'     => 'agency-list',
                )
            );

            foreach ($agencies as $agency_slug) {
                $agency = Agency_Editor::get_agency_by_slug($agency_slug);

                $url = admin_url('index.php');
                $url = add_query_arg(
                    array(
                        'set-agency-context' => $agency->slug,
                        '_wp_http_referer'   => $_SERVER['REQUEST_URI'],
                    ),
                    $url
                );

                $wp_admin_bar->add_menu(
                    array(
                        'parent' => 'agency-list',
                        'id'     => 'agency-' . $agency->slug,
                        'title'  => $agency->name,
                        'href'   => $url,
                    )
                );
            }
        }
    }

    // Show the permission group after current agency
    $wp_admin_bar->add_node(
        array(
            'parent' => 'top-secondary',
            'id'     => 'perm',
            'title'  => ucwords($role) . ', ' . $agency_role,
            'href'   => site_url() . '/wp-admin/profile.php?page=permissions-dashboard',
            'meta'   => [
                'title' => 'Permission group'
            ]
        )
    );
}

add_action('admin_init', 'set_agency_context');

function set_agency_context()
{
    global $pagenow;

    if (is_admin() && $pagenow == 'index.php' && isset($_GET['set-agency-context'])) {
        $set_agency = $_GET['set-agency-context'];

        $return = Agency_Context::set_agency_context($set_agency);

        if (is_wp_error($return)) {
            wp_die($return->get_error_message());
            exit;
        }

        wp_redirect(wp_get_referer());
    }
}

add_action('admin_bar_menu', 'region_context_switcher_menu', 98);

function region_context_switcher_menu($wp_admin_bar)
{
    if (! Region_Context::current_user_can_have_context()) {
        return false;
    }

    $context     = Region_Context::get_region_context();
    $region_name = Region_Context::get_region_context('name');

    $wp_admin_bar->add_node(
        array(
            'parent' => 'top-secondary',
            'id'     => 'region-context-switcher',
            'title'  => 'Region: ' . $region_name,
            'href'   => '#',
        )
    );

    $regions = Region_Context::current_user_available_regions();

    // Add sub-menu to switch context if there are multiple regions
    if (count($regions) > 1) {
        $wp_admin_bar->add_group(
            array(
                'parent' => 'region-context-switcher',
                'id'     => 'region-list',
            )
        );

        foreach ($regions as $region_slug) {
            $region = get_term_by('slug', $region_slug, 'region');

            $url = admin_url('index.php');
            $url = add_query_arg(
                array(
                    'set-region-context' => $region->slug,
                    '_wp_http_referer'   => $_SERVER['REQUEST_URI'],
                ),
                $url
            );

            $wp_admin_bar->add_menu(
                array(
                    'parent' => 'region-list',
                    'id'     => 'region-' . $region->slug,
                    'title'  => $region->name,
                    'href'   => $url,
                )
            );
        }
    }
}

add_action('admin_init', 'set_region_context');

function set_region_context()
{
    global $pagenow;

    if (is_admin() && $pagenow == 'index.php' && isset($_GET['set-region-context'])) {
        $set_region = $_GET['set-region-context'];

        $return = Region_Context::set_region_context($set_region);

        if (is_wp_error($return)) {
            wp_die($return->get_error_message());
            exit;
        }

        wp_redirect(wp_get_referer());
    }
}

/**
 * Show wp admin bar for region-editors
 */
add_action('init', 'show_admin_bar_for_regional');
function show_admin_bar_for_regional()
{

    if (is_user_logged_in() && get_role('regional-editor')) {
        show_admin_bar(true);
    }
}

/**
 * Remove wp admin bar for subscribers
 * Two add actions needed, one to remove custom menu items, other to remove wp admin bar
 */
add_action('admin_menu', 'hide_admin_bar_for_subscribers');
add_action('after_setup_theme', 'hide_admin_bar_for_subscribers');

function hide_admin_bar_for_subscribers()
{

    if (current_user_can('subscriber')) {
        show_admin_bar(false);
    }
}
