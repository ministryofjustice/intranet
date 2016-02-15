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

  $post_type = get_post_type();
  $controller = get_query_var('controller');
  $template = get_page_template();
  $path = get_query_var('param_string');

  //determine controller path
  if($template) {
    $controller_path = $template;
  }
  else {
    if(is_single()) {
      $controller = 'single-' . $post_type;
    }
    if(is_404()) {
      $controller = 'page_error';
    }
    $controller_path = get_template_directory() . '/' . $controller . '.php';
  }

  //include controller
  if(file_exists($controller_path)) {
    include($controller_path);
  }
  else {
    trigger_error('Controller not found: ' . $controller_path . '. Entry point: ' . $_SERVER['REQUEST_URI'], E_USER_ERROR);
  }

  //instantiate controller
  if($post_type != "document") {
    $controller_name = ucfirst(basename($controller_path));
    $controller_name = preg_replace('/\.[^.]+$/', '', $controller_name);
    $controller_name = str_replace('-', '_', $controller_name);

    if(class_exists($controller_name)) {
      new $controller_name($path);
      $MVC->run();
    }
    exit;
  }
}


function mvc_unhook_document_revisions_auth() {
  global $wpdr;
  if(isset($wpdr)) {
    remove_action('after_setup_theme', array(&$wpdr, 'auth_webdav_requests'));
  }
}

function mvc_query_vars() {
  add_rewrite_tag('%controller%', '([^&]+)');
  add_rewrite_tag('%param_string%', '([^&]+)');
}

if(!is_admin()) {
  add_action('init', 'mvc_query_vars', 1);
  add_action('wp', 'mvc_route');
  add_action('activated_plugin', 'mvc_load_first', 1);
  add_action('after_setup_theme', 'mvc_unhook_document_revisions_auth', 1);
}
