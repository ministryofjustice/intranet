<?php
// ----------------------------------------
// Controls post-types (custom and built-in)
// ----------------------------------------

// CPT DEFINITIONS
// ---------------

// Define webchat post type
function define_webchat_post_type() {
  register_post_type( 'webchat',
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
        'not_found_in_trash' => __('No webchats found in Trash')
      ),
      'description'   => __('Contains details of webchats'),
      'public'        => true,
      'menu_position' => 20,
      'menu_icon'     => 'dashicons-phone',
      'supports'      => array('title','editor','thumbnail','excerpt'),
      'has_archive'   => 'webchats'
    )
  );
}
add_action( 'init', define_webchat_post_type);

// CPT MODIFICATIONS
// -----------------

// Adds excerpts to pages
function add_page_excerpts() {
  add_post_type_support('page','excerpt' );
}
add_action('init','add_page_excerpts');

// Adds markdown support (wpcom-markdown) to Pods CPTs
function add_markdown_to_cpts() {
  $post_types = array('blog','event','glossaryitem','news','projects','task','vacancies');
  foreach ($post_types as $post_type) {
    add_post_type_support( $post_type, 'wpcom-markdown' );
  }
}
add_action('init', 'add_markdown_to_cpts');
