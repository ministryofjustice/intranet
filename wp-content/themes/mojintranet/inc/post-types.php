<?php
// ----------------------------------------
// Controls post-types (custom and built-in)
// ----------------------------------------

// CPT DEFINITIONS
// ---------------
$post_types_folder = 'post-types';
$post_types_array = array('webchat','event', 'news', 'regional-page', 'regional-news');
foreach($post_types_array as $post_type) {
  include_once($post_types_folder . '/' . $post_type . '.php');
}

// CPT MODIFICATIONS
// -----------------

// Adds excerpts to pages
function add_page_excerpts() {
  add_post_type_support('page','excerpt' );
}
add_action('init','add_page_excerpts');

// Adds markdown support (wpcom-markdown)
function add_markdown_to_cpts() {
  $post_types = array('event','news','webchat', 'regional-page', 'regional-news');
  foreach ($post_types as $post_type) {
    add_post_type_support( $post_type, 'wpcom-markdown' );
  }
}
add_action('init', 'add_markdown_to_cpts');
