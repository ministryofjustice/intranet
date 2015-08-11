<?php

// Add scripts for front-end
function enqueueThemeScripts() {
  global $wp_scripts;

  wp_enqueue_script( 'jquery' );
}

add_action('wp_enqueue_scripts','enqueueThemeScripts');

add_action('login_enqueue_scripts', 'my_custom_login_style');
// Adds custom styling to the login page
function my_custom_login_style() {
  wp_enqueue_style('custom_loginstyle', get_template_directory_uri() . '/admin/css/login.css');
}

add_action('admin_enqueue_scripts', 'pageparent_register_head');
// Enqueue style for pageparent dropdown
function pageparent_register_head($hook) {
  wp_enqueue_style('pageparent-style', get_template_directory_uri() . '/admin/css/pageparentstyle.css');
  if($hook == 'upload.php') {
    wp_enqueue_script('dw-crop-image',get_template_directory_uri() . '/admin/js/image-crop.js',array('image-edit'),99);
  }
}
