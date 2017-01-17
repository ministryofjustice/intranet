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

function dw_update_excerpt_field($value, $post_id, $field) {
  wp_update_post( ['ID' => $post_id, 'post_excerpt' => $value] );

  return $value;
}
add_filter('acf/update_value/name=dw_excerpt', 'dw_update_excerpt_field', 10, 3);

function dw_featured_image_msg($content, $post_ID, $thumbnail_id) {

  if(get_post_type($post_ID) == 'page') {
    $content = 'This image is displayed in the Featured section of the homepage' . $content;
  }
  return $content;
}
add_filter('admin_post_thumbnail_html', 'dw_featured_image_msg', 10, 3);



