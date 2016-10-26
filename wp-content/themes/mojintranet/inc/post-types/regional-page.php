<?php
// Define regional page post type
function define_regional_page_post_type() {
  register_post_type( 'regional_page',
    array(
      'labels'        => array(
        'name'               => __('Regional Pages'),
        'singular_name'      => __('Page'),
        'add_new_item'       => __('Add New Page'),
        'edit_item'          => __('Edit Page'),
        'new_item'           => __('New Page'),
        'view_item'          => __('View Page'),
        'search_items'       => __('Search Pages'),
        'not_found'          => __('No Pages found'),
        'not_found_in_trash' => __('No Pages found in Trash')
      ),
      'description'   => __('Contains details of Pages'),
      'public'        => true,
      'hierarchical' => true,
      'menu_position' => 3,
      'supports'      => array('title','editor','thumbnail','excerpt','author'),
      'has_archive'   => false,
      'rewrite'       => array(
        'slug'       => 'pages',
        'with_front' => false
      ),
      'capability_type' => array('regional_page', 'regional_pages')
    )
  );
}
add_action( 'init', 'define_regional_page_post_type');

