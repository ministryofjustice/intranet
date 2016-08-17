<?php
require_once(ABSPATH . 'wp-admin/includes/screen.php');
if( function_exists('acf_add_options_page') ) {

  acf_add_options_page(array(
  'page_title' 	=> 'Quick Links Settings',
		'menu_title'	=> 'Quick Links',
		'menu_slug' 	=> 'quick-links-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
  ));
}

add_action( 'init', 'dw_add_most_visited_options' );

function dw_add_most_visited_options() {

  $context = Agency_Context::get_agency_context();

  if($context == 'hq' && function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Guidance Most Visited Settings',
        'menu_title' => 'Guidance Most Visited',
        'menu_slug' => 'guidance-most-visted-settings',
        'capability' => 'edit_posts',
        'redirect' => false
    ));
  }

}

function my_acf_load_field( $field ) {

  $screen = get_current_screen();

  if($screen->id == 'toplevel_page_quick-links-settings' || $screen->id == 'toplevel_page_guidance-most-visted-settings') {
    $context = Agency_Context::get_agency_context();

    $field['name'] = $context . '_' . $field['name'];
  }

  return $field;

}

add_filter('acf/load_field/key=field_57b1bd28c275f', 'my_acf_load_field');
add_filter('acf/load_field/key=field_57b1cb89000f5', 'my_acf_load_field');

// Hide CPT menus
function dw_remove_menu_items() {
    if( !current_user_can( 'administrator' ) ):
      // remove_menu_page( 'edit.php' );
      remove_menu_page( 'edit-comments.php' );
      remove_menu_page( 'tools.php' );
      remove_menu_page( 'themes.php' );
    endif;
}
add_action( 'admin_menu', 'dw_remove_menu_items' );

// Programatically remove custom post types (natively and PODS)
function dw_remove_post_types() {
  global $wp_post_types;
  foreach( array( 'task','projects','vacancies','blog','glossaryitem' ) as $post_type ) {
    if ( isset( $wp_post_types[ $post_type ] ) ) {
      unset( $wp_post_types[ $post_type ] );
    }
  }
}
add_action( 'init', 'dw_remove_post_types', 20 );

// Add Customise menu item
function dw_add_customise() {
  add_menu_page( 'Customise', 'Customise', 'edit_theme_options', 'customize.php');
}
add_action( 'admin_menu', 'dw_add_customise');

// Prevent customiser redirecting back to themes page
function dw_customize_redirect() {
  if ( strstr( wp_get_referer(), '/wp-admin/customize.php' ) )
  {
    $url = get_admin_url(); // EDIT this to your needs
    wp_safe_redirect( $url );
    exit();
  }
}
add_action( 'load-themes.php', 'dw_customize_redirect' );

// Apply site styling to post editor
function dw_add_editor_style() {
    add_editor_style( 'assets/css/style.css' );
}
add_action( 'after_setup_theme', 'dw_add_editor_style' );

// Filters all buttons from TinyMCE editor to hide toolbar for non-admins
function dw_tinymce_settings($settings){
  // print_r($settings);
  if ( !current_user_can( 'manage_options' ) ) {
    $settings['toolbar1'] = array();
    $settings['toolbar2'] = array();
    return $settings;
  } else {
    return $settings;
  }
}
add_filter( 'tiny_mce_before_init', 'dw_tinymce_settings' );

// Customize the format dropdown items
if( !function_exists('base_custom_mce_format') ){
  function base_custom_mce_format($init) {
    // Add block format elements you want to show in dropdown
    $init['theme_advanced_blockformats'] = 'p,h2,h3,h4,h5,h6,pre,blockquote';
    //$init['extended_valid_elements'] = 'code[*]';
    return $init;
  }
  add_filter('tiny_mce_before_init', 'base_custom_mce_format' );
}

// add custom styles from editor-style.css to TinyMCE menu
function add_my_editor_style() {
  add_editor_style();
  wp_register_style('jquery-admin-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/overcast/jquery-ui.css', false, 0.1, false);
}
add_action( 'admin_init', 'add_my_editor_style' );

// disable the visual editor globally
add_filter( 'user_can_richedit', '__return_false' );

// Remove feeds
function kill_feed_rewrites($rules){
  foreach ($rules as $rule => $rewrite) {
    if ( preg_match('/.*feed/',$rule) ) {
      unset($rules[$rule]);
    }
  }
  return $rules;
}
add_filter('rewrite_rules_array', 'kill_feed_rewrites');

// Remove unecessary <link rel> tags
remove_action('wp_head', 'rsd_link'); //removes EditURI/RSD (Really Simple Discovery) link.
remove_action('wp_head', 'wlwmanifest_link'); //removes wlwmanifest (Windows Live Writer) link.
remove_action('wp_head', 'wp_generator'); //removes meta name generator.
remove_action('wp_head', 'wp_shortlink_wp_head'); //removes shortlink.
remove_action('wp_head', 'feed_links', 2 ); //removes feed links.
remove_action('wp_head', 'feed_links_extra', 3 ); //removes comments feed. 
remove_action('wp_head', 'rest_output_link_wp_head', 10 ); //removes WordPress REST API
