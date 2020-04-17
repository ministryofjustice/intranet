<?php
// Define condolences post type
function define_condolences_post_type()
{
    register_post_type(
        'condolences',
        array(
            'labels'          => array(
                'name'               => __('Condolences'),
                'singular_name'      => __('Condolence page'),
                'add_new_item'       => __('Add New Condolence page'),
                'edit_item'          => __('Edit Condolence page'),
                'new_item'           => __('New Condolence page'),
                'view_item'          => __('View Condolence page'),
                'search_items'       => __('Search Condolence pages'),
                'not_found'          => __('No Condolence pages found'),
                'not_found_in_trash' => __('No Condolence pages found in Trash'),
            ),
            'description'     => __('Contains details of Condolence pages'),
            'public'          => true,
            'menu_position'   => 1,
            'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
            'has_archive'     => false,
            'menu_icon'       => 'dashicons-heart',
            'rewrite'         => array(
                'slug'       => 'book-of-condolences',
                'with_front' => false,
            ),
            'hierarchical'    => false,
            
        )
    );
}
add_action('init', 'define_condolences_post_type');


/**
 *
 * Remove side meta boxes on Condolences entry admin page - these have been replaced in ACF with ACF fields
 *
 * */
add_action('do_meta_boxes', 'remove_metaboxes_condolences');

function remove_metaboxes_condolences()
{
        remove_meta_box('workplacediv', 'condolences', 'side');

}