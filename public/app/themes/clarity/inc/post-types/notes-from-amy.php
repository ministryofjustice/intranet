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
            add_menu_page(
                __('Notes from Amy', 'clarity'),
                'Notes from Amy',
                'notes_from_amy',
                'edit.php?post_type=note-from-amy',
                '',
                'dashicons-welcome-write-blog'
            );

            add_submenu_page(
                'edit.php?post_type=note-from-amy',
                'Add  Note',
                'Add New Note',
                'notes_from_amy',
                'post-new.php?post_type=note-from-amy'
            );
        });
    }
});

// Hook into this post_type, we need to detect
// new notes and apply the agencies that have
// access to the main page...

/**
 * Copy tagged agencies from 'Notes from Amy' page to individual Notes.
 *
 * Agencies have the ability to include content on their own Intranets. If they
 * choose Notes from Amy then each individual Note will need to reflect
 * this, otherwise it won't show up in search results for them.
 *
 * @param null $note
 */
function moj_intranet_copy_agencies_to_notes_from_amy($note = null)
{
    $agencies = [];
    $notes = [$note];

    // get agencies attached to the page
    // this is our source of truth...
    $page = get_page_by_path('notes-from-amy');
    foreach (wp_get_object_terms($page->ID, 'agency') as $agency) {
        $agencies[] = $agency->slug;
    }

    if (!$note) {
        // get all notes
        $notes = get_posts([
            'post_type' => 'note-from-amy',
            'numberposts' => -1
        ]);
    }

    foreach ($notes as $note) {
        // check if agencies match the current saved agencies...
        $terms = get_the_terms($note->ID, 'agency');
        $agencies_current = [];
        foreach ($terms as $agency) {
            $agencies_current[] = $agency->slug;
        }

        // we are checking if the agency arrays are different
        // if they are, we will make changes, otherwise, do nothing.
        if (!empty(array_diff($agencies, $agencies_current))) {
            // set as defined
            $terms = wp_set_object_terms($note->ID, $agencies, 'agency');

            if (is_wp_error($terms)) {
                trigger_error("Terms could not be added for a note with an ID of: " . $note->ID);
            }
        }
    }
}



add_action('notes_from_amy_cron_hook', 'moj_intranet_copy_agencies_to_notes_from_amy');

if (!wp_next_scheduled('notes_from_amy_cron_hook')) {
    wp_schedule_event(
        time(),
        (getenv('WP_ENV') === 'production' ? 'twicedaily' : 'one_minute'),
        'notes_from_amy_cron_hook'
    );
}
