<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

class AddNotesFromAntoniaCapabilities
{
    protected $capabilities = [
        'note_from_antonia',
        'notes_from_antonia',
        'edit_notes_from_antonia',
        'edit_note_from_antonia',
        'edit_others_notes_from_antonia',
        'edit_published_notes_from_antonia',
        'publish_notes_from_antonia',
        'delete_notes_from_antonia',
        'delete_published_notes_from_antonia',
        'delete_private_notes_from_antonia',
        'delete_note_from_antonia',
        'delete_others_notes_from_antonia'
    ];

    public function __construct()
    {
        $administrator_role = get_role('administrator');

        foreach ($this->capabilities as $cap) {
            if (empty($administrator_role->capabilities[$cap])) {
                $administrator_role->add_cap($cap);
            }
        }
    }
}
