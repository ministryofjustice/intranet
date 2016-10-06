<?php
/**
 * Adds Page Option Field name with the current Agency Context
 * Filter: acf/load_field
 *
 * @param array $field - the acf field that is being loaded
 */
function dw_agency_page_option_fields($field) {
  $screen = get_current_screen();

  if($screen->id == 'page') {
    $context = Agency_Context::get_agency_context();

    $field['name'] = 'dw_'. $context .'_guidance_bottom';
  }

  return $field;
}
add_filter('acf/load_field/key=field_57e92cb88452d', 'dw_agency_page_option_fields');
