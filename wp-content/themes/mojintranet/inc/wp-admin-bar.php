<?php

/**
 * Control the WP Admin Bar
 */

/**
 * Agency Context Switcher
 */
function agency_context_switcher_menu($wp_admin_bar) {
    if (!Agency_Context::current_user_can_have_context()) {
        return false;
    }

    $context = Agency_Context::get_agency_context();
    $agency = Agency_Editor::get_agency_by_slug($context);

    $wp_admin_bar->add_node(array(
        'parent'    => 'top-secondary',
        'id'        => 'agency-context-switcher',
        'title'     => 'Editing as: ' . $agency->name,
        'href'      => '#',
    ));

    $agencies = Agency_Context::current_user_available_agencies();

    // Add sub-menu to switch context if there are multiple agencies
    if (count($agencies) > 1) {
        $wp_admin_bar->add_group(array(
            'parent' => 'agency-context-switcher',
            'id'     => 'agency-list',
        ));

        foreach ($agencies as $agency) {
            $wp_admin_bar->add_menu(array(
                'parent' => 'agency-list',
                'id'     => 'agency-' . $agency->slug,
                'title'  => $agency->name,
                'href'   => add_query_arg('set-agency-context', $agency->slug),
            ));
        }
    }
}
add_action('admin_bar_menu', 'agency_context_switcher_menu');

function set_agency_context() {
    if (is_admin() && isset($_GET['set-agency-context'])) {
        $set_agency = $_GET['set-agency-context'];

        if (Agency_Context::current_user_can_change_to($set_agency)) {
            Agency_Context::set_agency_context($set_agency);
        }

        wp_redirect(remove_query_arg('set-agency-context'));
    }
}
add_action('admin_init', 'set_agency_context');
