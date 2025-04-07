<?php
add_action('init', function () {
    register_post_type('note-from-antonia', array(
        'labels' => array(
            'name' => 'Notes from Antonia',
            'singular_name' => 'Note',
            'menu_name' => 'Notes from Antonia',
            'all_items' => 'Notes from Antonia',
            'edit_item' => 'Edit Note',
            'view_item' => 'View Note',
            'view_items' => 'View Notes from Antonia',
            'add_new_item' => 'Add New Note',
            'new_item' => 'New Note',
            'parent_item_colon' => 'Parent Note:',
            'search_items' => 'Search Notes from Antonia',
            'not_found' => 'No notes from antonia found',
            'not_found_in_trash' => 'No notes from antonia found in Trash',
            'archives' => 'Note Archives',
            'attributes' => 'Note Attributes',
            'uploaded_to_this_item' => 'Uploaded to this note',
            'filter_items_list' => 'Filter notes from antonia list',
            'filter_by_date' => 'Filter notes from antonia by date',
            'items_list_navigation' => 'Notes from Antonia list navigation',
            'items_list' => 'Notes from Antonia list',
            'item_published' => 'Note published.',
            'item_published_privately' => 'Note published privately.',
            'item_reverted_to_draft' => 'Note reverted to draft.',
            'item_scheduled' => 'Note scheduled.',
            'item_updated' => 'Note updated.',
            'item_link' => 'Note Link',
            'item_link_description' => 'A link to a note.',
        ),
        'description' => 'Contains notes from Antonia Romeo, MoJs\' Permanent Secretary',
        'public' => true,
        'show_in_rest' => true,
        'rest_base' => 'notes-from-antonia',
        'show_in_menu' => false,
        'supports' => [
            'title',
            'editor',
            'revisions',
            'thumbnail',
            'excerpt'
        ],
        'rewrite' => array(
            'slug' => 'notes-from-antonia',
            'with_front' => false,
        ),
        'delete_with_user' => false,
        'capability_type' => [
            'note_from_antonia',
            'notes_from_antonia'
        ],
        'map_meta_cap' => true
    ));

    if (!Agency_Context::current_user_can_have_context()) {
        return false;
    }

    if (Agency_Context::get_agency_context() === 'hq') {
        add_action('admin_menu', function () {
            add_menu_page(
                __('Notes from Antonia', 'clarity'),
                'Notes from Antonia',
                'notes_from_antonia',
                'edit.php?post_type=note-from-antonia',
                '',
                'dashicons-welcome-write-blog'
            );

            add_submenu_page(
                'edit.php?post_type=note-from-antonia',
                'Add  Note',
                'Add New Note',
                'notes_from_antonia',
                'post-new.php?post_type=note-from-antonia'
            );
        });
    }
});

// Remove notes_from_antonia_cron_hook cron hook
add_action('init', function () {
    if (wp_next_scheduled('notes_from_antonia_cron_hook')) {
        wp_clear_scheduled_hook('notes_from_antonia_cron_hook');
    }
});
