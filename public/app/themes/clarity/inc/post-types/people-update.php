<?php

defined('ABSPATH') || die();

add_action('init', function () {

    $is_opg = Agency_Context::current_user_can_have_context() && Agency_Context::get_agency_context() === 'opg';

    register_post_type('people-update', [
        'labels' => [
            'name' => 'People Promise Update',
            'singular_name' => 'Update',
            'menu_name' => 'People Promise',
            'all_items' => 'All Updates',
            'edit_item' => 'Edit Update',
            'view_item' => 'View Update',
            'view_items' => 'View Updates',
            'add_new_item' => 'Add Update',
            'new_item' => 'New Update',
            'parent_item_colon' => 'Parent Update:',
            'search_items' => 'Search People Promise Updates',
            'not_found' => 'No updates found',
            'not_found_in_trash' => 'No updates found in Trash',
            'archives' => 'Update Archives',
            'attributes' => 'Update Attributes',
            'uploaded_to_this_item' => 'Uploaded to this note',
            'filter_items_list' => 'Filter People Promise Update list',
            'filter_by_date' => 'Filter People Promise Update by date',
            'items_list_navigation' => 'People Promise Update list navigation',
            'items_list' => 'People Promise Update list',
            'item_published' => 'Update published.',
            'item_published_privately' => 'Update published privately.',
            'item_reverted_to_draft' => 'Update reverted to draft.',
            'item_scheduled' => 'Update scheduled.',
            'item_updated' => 'People Promise Update, updated.',
            'item_link' => 'Update Link',
            'item_link_description' => 'A link to a note.',
        ],
        'description' => 'Contains People Promise Updates, created for OPG',
        'public' => true,
        'show_in_rest' => false,
        'show_in_menu' => $is_opg,
        'supports' => [
            'title',
            'editor',
            'revisions',
            'thumbnail',
            'excerpt'
        ],
        'delete_with_user' => false,
    ]);
});

// On page edit screen, show People Promise in the dropdown only when agency is OPG.
add_filter('theme_templates', function ($templates) {

    $is_opg = Agency_Context::current_user_can_have_context() && Agency_Context::get_agency_context() === 'opg';

    if ($is_opg) {
        // Agency is OPG, do noting.
        return $templates;
    }


    if (get_page_template_slug() === 'page_people_promise.php') {
        // Current template is 'page_people_promise.php', do nothing - else we could break a page.
        return $templates;
    }

    // Remove page_people_promise.php from the dropdown list.
    if (isset($templates['page_people_promise.php'])) {
        unset($templates['page_people_promise.php']);
        unset($templates['page_people_promise_archive.php']);
    }

    return $templates;
});
