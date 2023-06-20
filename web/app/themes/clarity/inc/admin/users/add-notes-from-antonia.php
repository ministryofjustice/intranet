<?php

add_action('init', 'notes_from_antonia_capabilities', 12);
add_action('admin_init', 'notes_from_antonia_capabilities', 12);

function notes_from_antonia_capabilities()
{
    global $wp_roles;

    $capabilities = [
        'administrator' => [
            'note_from_antonia' => true,
            'notes_from_antonia' => true,
            'edit_notes_from_antonia' => true,
            'edit_note_from_antonia' => true,
            'edit_others_notes_from_antonia' => true,
            'edit_published_notes_from_antonia' => true,
            'publish_notes_from_antonia' => true,
            'delete_notes_from_antonia' => true,
            'delete_published_notes_from_antonia' => true,
            'delete_private_notes_from_antonia' => true,
            'delete_note_from_antonia' => true,
            'delete_others_notes_from_antonia' => true
        ]
    ];

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
}
