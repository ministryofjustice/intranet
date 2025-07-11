<?php

add_action('init', function () {
    global $wp_roles;

    $capabilities = [
        'administrator' => [
            // Notes from Antonia
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
            'delete_others_notes_from_antonia' => true,
            // Notes from Amy
            'note_from_amy' => true,
            'notes_from_amy' => true,
            'edit_notes_from_amy' => true,
            'edit_note_from_amy' => true,
            'edit_others_notes_from_amy' => true,
            'edit_published_notes_from_amy' => true,
            'publish_notes_from_amy' => true,
            'delete_notes_from_amy' => true,
            'delete_published_notes_from_amy' => true,
            'delete_private_notes_from_amy' => true,
            'delete_note_from_amy' => true,
            'delete_others_notes_from_amy' => true,
            // Notes from Jo
            'note_from_jo' => true,
            'notes_from_jo' => true,
            'edit_notes_from_jo' => true,
            'edit_note_from_jo' => true,
            'edit_others_notes_from_jo' => true,
            'edit_published_notes_from_jo' => true,
            'publish_notes_from_jo' => true,
            'delete_notes_from_jo' => true,
            'delete_published_notes_from_jo' => true,
            'delete_private_notes_from_jo' => true,
            'delete_note_from_jo' => true,
            'delete_others_notes_from_jo' => true,
            // Archived Content
            'archived_content' => true
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
}, 12);
