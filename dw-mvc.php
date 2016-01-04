<?php

/*
  Plugin Name: dw-MVC
  Description: Adds MVC structure to WordPress code
  Author: Marcin Cichon
  Version: 0.1

  Changelog
  ---------
  0.1 - initial release
 */

if (!defined('ABSPATH')) {
  exit; // disable direct access
}

if (!class_exists('mmvc')) {
  class mmvc {
    function __construct() {
      define('MVC_PATH', get_template_directory().'/');
      define('MVC_VIEWS_DIR', 'views/');
      define('MVC_VIEWS_PATH', MVC_PATH.MVC_VIEWS_DIR);
      define('MVC_MODELS_DIR', 'models/');
      define('MVC_MODELS_PATH', MVC_PATH.MVC_MODELS_DIR);

      include_once(plugin_dir_path( __FILE__ ).'Loader.php');
      include_once(plugin_dir_path( __FILE__ ).'Controller.php');
      include_once(plugin_dir_path( __FILE__ ).'Model.php');
    }
  }

  new mmvc;
}

// Force mmvc plugin to loads before all others
function mmvc_load_first() {
  $path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
  if ( $plugins = get_option( 'active_plugins' ) ) {
    if ( $key = array_search( $path, $plugins ) ) {
      array_splice( $plugins, $key, 1 );
      array_unshift( $plugins, $path );
      update_option( 'active_plugins', $plugins );
    }
  }
}

function mmvc_init($template, $data = null) {
  global $MVC;

  include($template);

  $post_type = get_post_type();

  if($post_type!="document") {
    $controller_name = ucfirst(basename($template));
    $controller_name = preg_replace('/\.[^.]+$/', '', $controller_name);
    $controller_name = str_replace('-', '_', $controller_name);

    if(class_exists($controller_name)) {
      $MVC = new $controller_name($data);
      $MVC->load_models();
      $MVC->main();
    }
  }
}

add_action( 'activated_plugin', 'mmvc_load_first', 1);
