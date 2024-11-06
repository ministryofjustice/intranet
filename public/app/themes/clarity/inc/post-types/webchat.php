<?php

// Define webchat post type
function define_webchat_post_type()
{
    register_post_type(
        'webchat',
        array(
            'labels'        => array(
                'name'               => __('Webchats'),
                'singular_name'      => __('Webchat'),
                'add_new_item'       => __('Add New Webchat'),
                'edit_item'          => __('Edit Webchat'),
                'new_item'           => __('New Webchat'),
                'view_item'          => __('View Webchat'),
                'search_items'       => __('Search Webchats'),
                'not_found'          => __('No webchats found'),
                'not_found_in_trash' => __('No webchats found in Trash'),
            ),
            'description'   => __('Contains details of webchats'),
            'public'        => true,
            'menu_position' => 20,
            'menu_icon'     => 'dashicons-phone',
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
            'has_archive'   => false,
            'rewrite'       => array(
                'slug'       => 'webchats',
                'with_front' => false,
            ),
            'hierarchical'  => false,
        )
    );
}
add_action('init', 'define_webchat_post_type');

// Modify parent selector for webchats
add_action(
    'admin_menu',
    function () {
        remove_meta_box('pageparentdiv', 'webchat', 'normal');
    }
);
add_action(
    'add_meta_boxes',
    function () {
        add_meta_box('webchat-parent', 'Webchat Status', 'webchat_attributes_meta_box', 'webchat', 'side', 'high');
    }
);
function webchat_attributes_meta_box($post)
{
    $landing_page = get_page_by_path('webchats-hq', OBJECT, 'page');
    $archive_page = get_page_by_path('webchats-hq/archive', OBJECT, 'page');

    if ($landing_page && $archive_page) {
        ?>
      <input type="radio" name="parent_id" id="parent_id" value="<?php echo $landing_page->ID; ?>" <?php echo $post->post_parent != $archive_page->ID ? 'checked="yes"' : ''; ?>><label for="webchat_status">Live</label>&nbsp;
      <input type="radio" name="parent_id" id="parent_id" value="<?php echo $archive_page->ID; ?>" <?php echo $post->post_parent == $archive_page->ID ? 'checked="yes"' : ''; ?>><label for="webchat_status">Archive</label>
        <?php
    }
}

// Append archive to webchat permalink if archive selected
function append_query_string($post_link, $post)
{
    $archive_page = get_page_by_path('webchats-hq/archive', OBJECT, 'page');

    if (is_int($post)) {
        $post = get_post($post);
    }

    if (isset($archive_page) && $post->post_type == 'webchat' && $post->post_parent == $archive_page->ID) {
        return str_replace('/webchats/', '/webchats/archive', $post_link);
    } else {
        return $post_link;
    }
}
add_filter('get_sample_permalink', 'append_query_string', 10, 2);
