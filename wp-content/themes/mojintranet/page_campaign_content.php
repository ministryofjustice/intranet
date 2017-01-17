<?php if (!defined('ABSPATH')) die();

/**
 * The generic template with on/off LHS navigation
 *
 * Template name: Campaign content
 */

class Page_campaign_content extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();

      $this->post_ID = get_the_ID();

      $this->view('layouts/default', $this->get_data());
    }
  }

  function get_data(){
    $post = get_post($this->post_ID);

    ob_start();
    the_content();
    $content = ob_get_clean();

    $thumbnail_id = get_post_thumbnail_id($this->post_ID);
    $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'full');
    $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

    $banner_id = get_post_meta($this->post_id, 'dw_page_banner', true);
    $banner_url = get_post_meta($this->post_id, 'dw_banner_url', true);
    $banner_image = wp_get_attachment_image_src($banner_id, 'full');

    $colour_hex = get_post_meta($this->post_id, 'dw_campaign_colour', true);

    $lhs_menu_on = get_post_meta($post->ID, 'dw_lhs_menu_on', true) != "0" ? true : false;

    if ($lhs_menu_on) {
      $content_classes = 'col-lg-9 col-md-8 col-sm-12';
    }
    else {
      $content_classes =  'col-lg-9 col-md-12 col-sm-12';
    }

    return array(
      'page' => 'pages/campaign_content/main',
      'template_class' => 'campaign-content',
      'cache_timeout' => 60 * 60, /* 1 hour */
      'page_data' => array(
        'id' => $this->post_ID,
        'title' => get_the_title(),
        'thumbnail' => $thumbnail[0],
        'thumbnail_alt_text' => $alt_text,
        'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'content' => $content,
        'content_classes' => $content_classes,
        'lhs_menu_on' => $lhs_menu_on,
        'banner_image_url' => $banner_image[0],
        'banner_url' => $banner_url,
        'style_data' => [
          'campaign_colour' => $colour_hex
        ]
      )
    );
  }
}
