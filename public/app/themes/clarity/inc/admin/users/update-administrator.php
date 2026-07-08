<?php

global $wp_roles;

$capabilities = [
    'administrator' => [
        // Add ACF homepage feature capability called 'homepage_all_access'
        'homepage_all_access' => true
    ]
];

// Small multi-loop that helps to prevent
// unnecessary modifications on role capabilities
foreach ($capabilities as $role => $caps) {
    $target = $wp_roles->get_role($role);
    foreach ($caps as $cap => $value) {
        if ($value === true) {
            if (!isset($target->capabilities[$cap])) {
                $wp_roles->add_cap($role, $cap);
            }
        } else if ($value === false) {
            $wp_roles->remove_cap($role, $cap);
        }
    }
}
