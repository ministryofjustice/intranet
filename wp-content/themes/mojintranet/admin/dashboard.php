<?php

function dw_remove_dashboard_widgets() {
  global $wp_meta_boxes;

  unset($wp_meta_boxes['dashboard']['normal']['core']['recently-edited-content']);
}

add_action('wp_dashboard_setup', 'dw_remove_dashboard_widgets' );


