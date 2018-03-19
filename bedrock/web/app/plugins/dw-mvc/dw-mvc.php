<?php

/*
  Plugin Name: DW MVC
  Description: Adds MVC structure to WordPress code
  Author: Marcin Cichon
  Version: 0.2

  Changelog
  ---------
  0.1 - initial release
 */

if (!defined('ABSPATH')) {
    die();
}

if (is_admin()) {
    error_reporting(E_ALL ^ E_NOTICE);
}

class DW_MVC
{
    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(__FILE__);

        include_once($this->plugin_path.'Loader.php');
        include_once($this->plugin_path.'Controller.php');
        include_once($this->plugin_path.'Model.php');

        if (!is_admin()) {
            add_action('init', [&$this, 'action_query_vars'], 1);
            add_action('wp', [&$this, 'action_route']);
            add_action('activated_plugin', [&$this, 'action_load_first'], 1);
        }
    }

    public function action_route()
    {
        global $MVC;

        do_action('dw_redirect');

        $post_type = get_post_type();
        $controller = get_query_var('controller');
        $page_template = get_page_template();
        $regional_template = get_post_meta(get_the_ID(), 'dw_regional_template', true);
        $template = $regional_template ? get_template_directory() . '/' . $regional_template : $page_template;
        $path = get_query_var('param_string');
        $has_post = (boolean) !$controller;

    //determine controller path
    if ($template) {
        $controller_path = $template;
    } else {
        if (is_single()) {
            $controller = 'single-' . $post_type;
        }
        if (is_404()) {
            $controller = 'page_error';
            $method = 'error404';
            $has_post = false;
        }
        $controller_path = get_template_directory() . '/' . $controller . '.php';
    }

    /*
     * The MVC plugin uses the action_route() function above to determine the file paths
     * (or controller paths) needed for the MVC system to work as intended.
     * However this disrupts Wordpress' hierarchy system.
     * Here I restore the WP hierarchy model, specifying that files placed into the child theme,
     * will override their counterpart residing in the parent theme.
     * This is particular only to single.php files and other single WP post types.
     */

    // Getting the child theme relative file path.
      $child_theme_file = get_stylesheet_directory() . '/' . $controller . '.php';

    // Resolves to true if you are on a single page with a specified post type.
    if (is_singular(['post','news','event','regional_news','webchat'])) {
        // Checks if the file exists in the child theme and assigns a child theme path.
        if (file_exists($child_theme_file)) {
            $controller = 'single-' . $post_type;
            $controller_path = get_stylesheet_directory() . '/' . $controller . '.php';
        } else {
            // Uses the MVC controller path.
            $controller = 'single-' . $post_type;
            $controller_path = get_template_directory() . '/' . $controller . '.php';
        }
    }

    //include controller
    if (file_exists($controller_path)) {
        include($controller_path);
    } else {
        $controller = 'page_error';
        $method = 'error500';
        $has_post = false;
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
            $post_id = 0;

            if ($has_post) {
                $post_id = get_the_ID();
            }

            new $controller_name($path, $post_id);

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
  public function action_load_first()
  {
      $path = str_replace(WP_PLUGIN_DIR . '/', '', __FILE__);
      if ($plugins = get_option('active_plugins')) {
          if ($key = array_search($path, $plugins)) {
              array_splice($plugins, $key, 1);
              array_unshift($plugins, $path);
              update_option('active_plugins', $plugins);
          }
      }
  }

    public function action_query_vars()
    {
        add_rewrite_tag('%controller%', '([^&]+)');
        add_rewrite_tag('%param_string%', '([^&]+)');
    }
}

new DW_MVC();
