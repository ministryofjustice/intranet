<?php
// Define regional news post type
function define_regional_news_post_type() {
  register_post_type( 'regional_news',
    array(
      'labels'        => array(
        'name'               => __('Regional News'),
        'singular_name'      => __('News story'),
        'add_new_item'       => __('Add New News story'),
        'edit_item'          => __('Edit News story'),
        'new_item'           => __('New News story'),
        'view_item'          => __('View News story'),
        'search_items'       => __('Search News stories'),
        'not_found'          => __('No News stories found'),
        'not_found_in_trash' => __('No News stories found in Trash')
      ),
      'description'   => __('Contains details of News stories'),
      'public'        => true,
      'menu_position' => 3,
      'supports'      => array('title','editor','thumbnail','excerpt','author'),
      'has_archive'   => false,
      'rewrite'       => array(
        'slug'       => 'regional-news',
        'with_front' => false
      ),
      'hierarchical'  => false,
      'capability_type' => array('regional_news', 'regional_news')
    )
  );
}
add_action('init', 'define_regional_news_post_type');

