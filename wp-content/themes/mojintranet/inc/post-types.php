<?
// ----------------------------------------
// Controls post-types (custom and built-in)
// ----------------------------------------

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