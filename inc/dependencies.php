<?php

// Add scripts for front-end
function enqueueThemeScripts() {
  global $wp_scripts;

  wp_enqueue_script( 'jquery' );
}

add_action('wp_enqueue_scripts','enqueueThemeScripts');

// Adds custom styling to the login page
function my_custom_login_style() {
  wp_register_style('custom_loginstyle', get_template_directory_uri() . '/css/login.css');
  wp_enqueue_style("custom_loginstyle");
}
add_action('login_enqueue_scripts', 'my_custom_login_style');
