<?php
add_action('init', function () {
    register_post_type('note-from-amy', array(
        'labels' => array(
            'name' => 'Notes from Amy',
            'singular_name' => 'Note',
            'menu_name' => 'Notes from Amy',
            'all_items' => 'Notes from Amy',
            'edit_item' => 'Edit Note',
            'view_item' => 'View Note',
            'view_items' => 'View Notes from Amy',
            'add_new_item' => 'Add New Note',
            'new_item' => 'New Note',
            'parent_item_colon' => 'Parent Note:',
            'search_items' => 'Search Notes from Amy',
            'not_found' => 'No notes from Amy found',
            'not_found_in_trash' => 'No notes from amy found in Trash',
            'archives' => 'Note Archives',
            'attributes' => 'Note Attributes',
            'uploaded_to_this_item' => 'Uploaded to this note',
            'filter_items_list' => 'Filter notes from Amy list',
            'filter_by_date' => 'Filter notes from Amy by date',
            'items_list_navigation' => 'Notes from Amy list navigation',
            'items_list' => 'Notes from Amy list',
            'item_published' => 'Note published.',
            'item_published_privately' => 'Note published privately.',
            'item_reverted_to_draft' => 'Note reverted to draft.',
            'item_scheduled' => 'Note scheduled.',
            'item_updated' => 'Note updated.',
            'item_link' => 'Note Link',
            'item_link_description' => 'A link to a note.',
        ),
        'description' => 'Contains notes from Amy Romeo, MoJs\' Permanent Secretary',
        'public' => true,
        'show_in_rest' => true,
        'rest_base' => 'notes-from-amy',
        'show_in_menu' => false,
        'supports' => [
            'title',
            'editor',
            'revisions',
            'thumbnail',
            'excerpt'
        ],
        'rewrite' => array(
            'slug' => 'notes-from-amy',
            'with_front' => false,
        ),
        'delete_with_user' => false,
        'capability_type' => [
            'note_from_amy',
            'notes_from_amy'
        ],
        'map_meta_cap' => true
    ));

    if (!Agency_Context::current_user_can_have_context()) {
        return false;
    }

    if (Agency_Context::get_agency_context() === 'hq') {
        add_action('admin_menu', function () {
            add_submenu_page(
                'archived-content',
                __('Notes from Amy', 'clarity'),
                'Notes from Amy',
                'notes_from_amy',
                'edit.php?post_type=note-from-amy',
                '', // An empty callback
                2   // Menu position, below Notes from Antonia
            );
        });
    }
});
