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
      'supports'      => array('title','editor','thumbnail','excerpt','page-attributes'),
      'has_archive'   => false,
      'rewrite'       => array(
        'slug'       => 'webchats',
        'with_front' => false
      ),
      'hierarchical'  => false
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
  $post_types = array('blog','event','glossaryitem','news','projects','task','vacancies','webchat');
  foreach ($post_types as $post_type) {
    add_post_type_support( $post_type, 'wpcom-markdown' );
  }
}
add_action('init', 'add_markdown_to_cpts');

// Modify parent selector for webchats
add_action('admin_menu', function() { remove_meta_box('pageparentdiv', 'webchat', 'normal');});
add_action('add_meta_boxes', function() { add_meta_box('webchat-parent', 'Webchat Status', 'webchat_attributes_meta_box', 'webchat', 'side', 'high');});
function webchat_attributes_meta_box($post) {
  $post_type_object = get_post_type_object($post->post_type);
  // if ( $post_type_object->hierarchical ) {
    $landing_page = get_page_by_title( "Webchats", $output = OBJECT, $post_type = 'page' );
    $archive_page = get_page_by_title( "Webchats Archive", $output = OBJECT, $post_type = 'page' )?:get_page_by_title( "Webchats: archive", $output = OBJECT, $post_type = 'page' );

    if($landing_page && $archive_page) {
      ?>
      <input type="radio" name="parent_id" id="parent_id" value="<?=$landing_page->ID?>" <?=$post->post_parent!=$archive_page->ID?'checked="yes"':''?>><label for="webchat_status">Live</label>&nbsp;
      <input type="radio" name="parent_id" id="parent_id" value="<?=$archive_page->ID?>" <?=$post->post_parent==$archive_page->ID?'checked="yes"':''?>><label for="webchat_status">Archive</label>
      <?php
    }
  // } // end hierarchical check.

// Append archive to webchat permalink if archive selected
function append_query_string($post_link,$post) {
  $archive_page = get_page_by_title( "Webchats Archive", $output = OBJECT, $post_type = 'page' );
  if($post->post_type=='webchat' && $post->post_parent==$archive_page->ID) {
    return str_replace('/webchats/', '/webchats/archive', $post_link);
  } else {
    return $post_link;
  }
}
add_filter('get_sample_permalink', 'append_query_string',10,2);
}
