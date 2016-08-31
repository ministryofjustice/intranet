<?php
// Define news post type
function define_news_post_type() {

  register_post_type( 'news',
    array(
      'labels'        => array(
        'name'               => __('News stories'),
        'singular_name'      => __('News story'),
        'add_new_item'       => __('Add New News story'),
        'edit_item'          => __('Edit News story'),
        'new_item'           => __('New News story'),
        'view_item'          => __('View News story'),
        'search_items'       => __('Search Events'),
        'not_found'          => __('No News stories found'),
        'not_found_in_trash' => __('No News stories found in Trash')
      ),
      'description'   => __('Contains details of News stories'),
      'public'        => true,
      'menu_position' => 3,
      'supports'      => array('title','editor','thumbnail','excerpt'),
      'has_archive'   => false,
      'rewrite'       => array(
        'slug'       => 'news',
        'with_front' => false
      ),
      'hierarchical'  => false,
      'capability_type' => array('news', 'news')
    )
  );

}
add_action( 'init', 'define_news_post_type');

