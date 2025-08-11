<?php
add_action('init', function () {
    register_post_type('note-from-jo', [
        'labels' => [
            'name' => 'Notes from Jo',
            'singular_name' => 'Note',
            'menu_name' => 'Notes from Jo',
            'all_items' => 'All Notes',
            'edit_item' => 'Edit Note',
            'view_item' => 'View Note',
            'view_items' => 'View Notes from Jo',
            'add_new_item' => 'Add New Note',
            'new_item' => 'New Note',
            'parent_item_colon' => 'Parent Note:',
            'search_items' => 'Search Notes from Jo',
            'not_found' => 'No notes from Jo found',
            'not_found_in_trash' => 'No notes from Jo found in Trash',
            'archives' => 'Note Archives',
            'attributes' => 'Note Attributes',
            'uploaded_to_this_item' => 'Uploaded to this note',
            'filter_items_list' => 'Filter notes from Jo list',
            'filter_by_date' => 'Filter notes from Jo by date',
            'items_list_navigation' => 'Notes from Jo list navigation',
            'items_list' => 'Notes from Jo list',
            'item_published' => 'Note published.',
            'item_published_privately' => 'Note published privately.',
            'item_reverted_to_draft' => 'Note reverted to draft.',
            'item_scheduled' => 'Note scheduled.',
            'item_updated' => 'Note updated.',
            'item_link' => 'Note Link',
            'item_link_description' => 'A link to a note.',
        ],
        'description' => 'Contains notes from Jo Romeo, MoJs\' Permanent Secretary',
        'public' => true,
        'show_in_rest' => true,
        'rest_base' => 'notes-from-jo',
        'show_in_menu' => false,
        'supports' => [
            'title',
            'editor',
            'revisions',
            'thumbnail',
            'excerpt'
        ],
        'rewrite' => array(
            'slug' => 'notes-from-jo',
            'with_front' => false,
        ),
        'delete_with_user' => false,
        'capability_type' => [
            'note_from_jo',
            'notes_from_jo'
        ],
        'map_meta_cap' => true
    ]);

    if (!Agency_Context::current_user_can_have_context()) {
        return false;
    }

    if (Agency_Context::get_agency_context() === 'hq') {
        add_action('admin_menu', function () {
            add_menu_page(
                __('Notes from Jo', 'clarity'),
                'Notes from Jo',
                'notes_from_jo',
                'edit.php?post_type=note-from-jo',
                '',
                'dashicons-welcome-write-blog'
            );

            add_submenu_page(
                'edit.php?post_type=note-from-jo',
                'Add  Note',
                'Add New Note',
                'notes_from_jo',
                'post-new.php?post_type=note-from-jo'
            );
        });
    }
});
