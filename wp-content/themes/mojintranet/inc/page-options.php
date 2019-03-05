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


function dw_featured_image_msg($content, $post_ID, $thumbnail_id) {

  if(get_post_type($post_ID) == 'page') {
    $content = 'This image is displayed in the Featured section of the homepage' . $content;
  }
  return $content;
}
add_filter('admin_post_thumbnail_html', 'dw_featured_image_msg', 10, 3);

function dw_save_excerpt($data , $postarr) {
  global  $post;

  if(!empty($_POST["acf"])) {

    foreach ($_POST["acf"] as $acf_key => $value) {
          $acf_field = get_field_object($acf_key);

          if($acf_field["name"] == "dw_excerpt") {
            if ((empty($post) && !empty($value)) || !empty($post)) {
              $data["post_excerpt"] = $value;
            }

          }
    }
  }
  return $data;
}
add_filter('wp_insert_post_data', 'dw_save_excerpt', '99', 2);


function dw_load_excerpt( $value, $post_id, $field )
{
  $content_post = get_post($post_id);
  $value = $content_post->post_excerpt;

  return $value;
}

add_filter('acf/load_value/name=dw_excerpt', 'dw_load_excerpt', 10, 3);
