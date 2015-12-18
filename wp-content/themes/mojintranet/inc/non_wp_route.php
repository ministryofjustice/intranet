<?php if (!defined('ABSPATH')) die();

function non_wp_route() {
  $controller = get_query_var('controller');
  $path = get_query_var('param_string');

  $controller_path = get_template_directory() . '/' . $controller . '.php';

  if(file_exists($controller_path)) {
    mmvc_init($controller_path, $path);
  }

  exit;
}

add_action('wp', 'non_wp_route');
