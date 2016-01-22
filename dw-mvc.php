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

/** leaving this in for now as some of the dw plugins still check its existence
 */
if (!class_exists('mmvc')) {
  class mmvc {}
}

define('MVC_PATH', get_template_directory().'/');
define('MVC_VIEWS_DIR', 'views/');
define('MVC_VIEWS_PATH', MVC_PATH.MVC_VIEWS_DIR);
define('MVC_MODELS_DIR', 'models/');
define('MVC_MODELS_PATH', MVC_PATH.MVC_MODELS_DIR);

$plugin_path = plugin_dir_path( __FILE__ );

include_once($plugin_path.'Loader.php');
include_once($plugin_path.'Controller.php');
include_once($plugin_path.'Model.php');

// Force mmvc plugin to loads before all others
function mvc_load_first() {
  $path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
  if ( $plugins = get_option( 'active_plugins' ) ) {
    if ( $key = array_search( $path, $plugins ) ) {
      array_splice( $plugins, $key, 1 );
      array_unshift( $plugins, $path );
      update_option( 'active_plugins', $plugins );
    }
  }
}

function mvc_route() {
  global $MVC;

  $controller = get_query_var('controller');
  $path = get_query_var('param_string');
  $post_type = get_post_type();

  //!!! To be refactored
  if(is_single()) {
    $controller_path = get_template_directory() . '/single-' . $post_type . '.php';
  }
  else {
    $controller_path = $controller ? get_template_directory() . '/' . $controller . '.php' : get_page_template();
  }

  include($controller_path);

  if($post_type != "document") {
    $controller_name = ucfirst(basename($controller_path));
    $controller_name = preg_replace('/\.[^.]+$/', '', $controller_name);
    $controller_name = str_replace('-', '_', $controller_name);

    if(class_exists($controller_name)) {
      $MVC = new $controller_name($path);
      $MVC->load_models();
      $MVC->main();
    }
  }

  exit;
}

function mvc_query_vars() {
  add_rewrite_tag('%controller%', '([^&]+)');
  add_rewrite_tag('%param_string%', '([^&]+)');
}

if(!is_admin()) {
  add_action('init', 'mvc_query_vars', 1);
  add_action('wp', 'mvc_route');
  add_action('activated_plugin', 'mvc_load_first', 1);
}
