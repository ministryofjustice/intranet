<?php
require_once(ABSPATH . 'wp-admin/includes/screen.php');

/**
 * Adds Quick Links and Most Visited Option Pages
 * Filter: init
 */
add_action('init', 'dw_add_option_pages');

function dw_add_option_pages() {
  if (function_exists('acf_add_options_page')) {

    $context = Agency_Context::get_agency_context();

    if ($context == 'hmcts') {
      acf_add_options_page([
        'page_title' 	=> 'My Work Links Settings',
        'menu_title'	=> 'My Work Links',
        'menu_slug' 	=> 'my-work-links-settings',
        'capability'	=> 'edit_posts',
        'redirect'		=> false
      ]);
    }

  }
}

/**
 * Should really be refactored into above..
 */
add_filter('acf/load_field/key=field_58bd431b4f6ac', 'dw_mw_agency_option_fields');

function dw_mw_agency_option_fields($field) {
  $screen = get_current_screen();

  if(isset($screen) && ($screen->id == 'toplevel_page_my-work-links-settings')) {
    $context = Agency_Context::get_agency_context();
    $field['name'] = $context . '_' . $field['name'];
}
  return $field;
}
