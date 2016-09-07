<?php

/*
  Plugin Name: DW MVC
  Description: Adds MVC structure to WordPress code
  Author: Marcin Cichon
  Version: 0.1

  Changelog
  ---------
  0.1 - initial release
 */

if (!defined('ABSPATH')) die();

class DW_MVC {
  public function __construct() {
    $this->plugin_path = plugin_dir_path( __FILE__ );

    include_once($this->plugin_path.'Loader.php');
    include_once($this->plugin_path.'Controller.php');
    include_once($this->plugin_path.'Model.php');

    if (!is_admin()) {
      add_action('init', [&$this, 'action_query_vars'], 1);
      add_action('wp', [&$this, 'action_route']);
      add_action('activated_plugin', [&$this, 'action_load_first'], 1);
    }
  }

  function action_route() {
    global $MVC;

    do_action('dw_redirect');

    $post_type = get_post_type();
    $controller = get_query_var('controller');
    $template = get_page_template();
    $path = get_query_var('param_string');

    //determine controller path
    if ($template) {
      $controller_path = $template;
    }
    else {
      if (is_single()) {
        $controller = 'single-' . $post_type;
      }
      if (is_404()) {
        $controller = 'page_error';
        $method = 'error404';
      }
      $controller_path = get_template_directory() . '/' . $controller . '.php';
    }

    //include controller
    if (file_exists($controller_path)) {
      include($controller_path);
    }
    else {
      $controller = 'page_error';
      $method = 'error500';
      $original_controller_path = $controller_path;
      $controller_path = get_template_directory() . '/'. $controller . '.php';
      include($controller_path);
    }

    //instantiate controller
    if ($post_type != "document") {
      $controller_name = ucfirst(basename($controller_path));
      $controller_name = preg_replace('/\.[^.]+$/', '', $controller_name);
      $controller_name = str_replace('-', '_', $controller_name);

      if (class_exists($controller_name)) {
        new $controller_name($path);

        if (isset($method)) {
          $MVC->method = $method;
        }

        if (isset($original_controller_path)) {
          $MVC->original_controller_path = $original_controller_path;
        }

        $MVC->run();
      }
      exit;
    }
  }

  // Force mvc plugin to load before other plugins
  function action_load_first() {
    $path = str_replace(WP_PLUGIN_DIR . '/', '', __FILE__);
    if ($plugins = get_option('active_plugins')) {
      if ($key = array_search($path, $plugins)) {
        array_splice($plugins, $key, 1);
        array_unshift($plugins, $path);
        update_option('active_plugins', $plugins);
      }
    }
  }

  function action_query_vars() {
    add_rewrite_tag('%controller%', '([^&]+)');
    add_rewrite_tag('%param_string%', '([^&]+)');
  }
}

new DW_MVC();
