<?php if (!defined('ABSPATH')) die();

/**
 * Template name: Media grid
 */

class Page_media_grid extends MVC_controller {
  function main(){
    $this->model('media_grid');

    while(have_posts()){
      the_post();

      $this->post_ID = get_the_ID();

      $this->view('layouts/default', $this->get_data());
    }
  }

  /** Code to deal with wp previewing revisions? ***/
  function get_data(){
    if (get_array_value($_GET, 'preview', 'false') == 'true') {
      $revisions = wp_get_post_revisions($this->post_ID);

      if (count($revisions) > 0) {
        $latest_revision = array_shift($revisions);
        $this->post_ID = $latest_revision->ID;
      }
    }

    $post = get_post($this->post_ID);

    ob_start();
    the_content();
    $content = ob_get_clean();

    $banner_id = get_post_meta($this->post_ID, 'dw_page_banner', true);
    $banner_url = get_post_meta($this->post_ID, 'dw_banner_url', true);
    $banner_image = wp_get_attachment_image_src($banner_id, 'full');

    $staff_capture_title = get_post_meta($this->post_ID, 'section_title', true);

    $colour_hex = get_post_meta($this->post_ID, 'dw_campaign_colour', true);

    $lhs_menu_on = get_post_meta($this->post_ID, 'dw_lhs_menu_on', true) != "0" ? true : false;

    if ($lhs_menu_on) {
      $content_classes = 'col-lg-9 col-md-8 col-sm-12';
    }
    else {
      $content_classes =  'col-lg-9 col-md-12 col-sm-12';
    }

    return array(
      'page' => 'pages/media_grid/main',
      'template_class' => 'media-grid',
      'cache_timeout' => 60 * 60, /* 1 hour */
      'page_data' => array(
        'id' => $this->post_ID,
        'title' => get_the_title(),
        'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'content' => $content,
        'content_classes' => $content_classes,
        'lhs_menu_on' => $lhs_menu_on,
        'section_title' => $staff_capture_title,
        'banner_image_url' => $banner_image[0],
        'banner_url' => $banner_url,
        'style_data' => [
          'campaign_colour' => $colour_hex
        ]
      )
    );
  }
}
