<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

class AddAcfCapabilities
{
    public function __construct()
    {
        global $wp_roles;

        $wp_roles->add_cap('administrator', 'homepage_all_access');
        $wp_roles->add_cap('agency_admin', 'homepage_all_access');
        $wp_roles->add_cap('agency_editor', 'homepage_all_access');
    }
}
