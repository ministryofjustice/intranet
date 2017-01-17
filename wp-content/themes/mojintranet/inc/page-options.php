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

function add_colour_contrast_script($hook) {
  global $post;

  if ($hook == 'post-new.php' || $hook == 'post.php') {
    if ('page' === $post->post_type) {
      wp_register_script('colour-contrast', get_stylesheet_directory_uri().'/admin/js/colour-contrast-checker.js');
      wp_localize_script('colour-contrast', 'params', ['ajax_url' => admin_url('admin-ajax.php')]);
      wp_enqueue_script('colour-contrast');
    }
  }
}
add_action( 'admin_enqueue_scripts', 'add_colour_contrast_script', 10, 1 );

function dw_add_contrast_message($field) {
  if (!empty($field['wrapper']) && !empty($field['wrapper']['class']) && strpos($field['wrapper']['class'], 'colour_check') !== false) {
    echo '<div class="acf-error-message contrast_invalid_message"><p>The colour you have entered does not meet the AA accessibility requirements :( Go to http://colorsafe.co for help picking an accessible colour.</p></div>';
    echo '<div class="acf-error-message contrast_valid_message"><p>The colour you have entered meets our AA accessibility requirements :)</p></div>';
  }
}
add_action( 'acf/render_field', 'dw_add_contrast_message', 10, 1);

function dw_check_colour_contrast() {
  $colour1 = $_GET['colour1'];
  $colour2 = $_GET['colour2'];
  $success = false;
  $contrast = 0;

  if(!empty($colour1) && !empty($colour2)) {
    $contrast = dw_colour_diff(dw_hex_to_rgb($colour1), dw_hex_to_rgb($colour2));
    $success = true;
  }

  echo json_encode(['success'  => $success, 'contrast' => $contrast]);
  die();
}
add_action('wp_ajax_check_colour_contrast', 'dw_check_colour_contrast' );
add_action('wp_ajax_nopriv_check_colour_contrast', 'dw_check_colour_contrast' );

function dw_hex_to_rgb($hex) {
  $hex = str_replace("#", "", $hex);

  if (strlen($hex) == 3) {
    $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
    $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
    $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
  }
  else {
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
  }
  $rgb = array('r' => $r, 'g' => $g, 'b' => $b);
  return $rgb; // returns an array with the rgb values
}

function dw_colour_diff($colour1, $colour2){
  return max($colour1['r'], $colour2['r']) - min($colour1['r'], $colour2['r']) +
  max($colour1['g'], $colour2['g']) - min($colour1['g'], $colour2['g']) +
  max($colour1['b'], $colour2['b']) - min($colour1['b'], $colour2['b']);
}

function dw_check_colour_field($value, $post_id, $field) {
  if (substr($value, 0, 1) != "#") {
    $value = '#' . $value;
  }
  return $value;
}
add_filter('acf/update_value/key=field_587649cf122f3', 'dw_check_colour_field', 10, 3);

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
