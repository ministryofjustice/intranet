<?php


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

// disable the visual editor globally
add_filter( 'user_can_richedit', '__return_false' );

// Remove unecessary <link rel> tags
remove_action('wp_head', 'rsd_link'); //removes EditURI/RSD (Really Simple Discovery) link.
remove_action('wp_head', 'wlwmanifest_link'); //removes wlwmanifest (Windows Live Writer) link.
remove_action('wp_head', 'wp_generator'); //removes meta name generator.
remove_action('wp_head', 'wp_shortlink_wp_head'); //removes shortlink.
remove_action('wp_head', 'feed_links', 2 ); //removes feed links.
remove_action('wp_head', 'feed_links_extra', 3 ); //removes comments feed.
remove_action('wp_head', 'rest_output_link_wp_head', 10 ); //removes WordPress REST API