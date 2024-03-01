<?php

add_action('init', 'homepage_capabilities', 11);

function homepage_capabilities()
{

    // Add ACF homepage feature capability called 'homepage_all_access'
    global $wp_roles;

    $wp_roles->add_cap('administrator', 'homepage_all_access');
    $wp_roles->add_cap('agency_admin', 'homepage_all_access');
    $wp_roles->add_cap('agency_editor', 'homepage_all_access');
}
