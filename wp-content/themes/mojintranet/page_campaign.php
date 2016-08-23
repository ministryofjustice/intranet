<?php if (!defined('ABSPATH')) die();

/**
 * The generic template without LHS navigation
 *
 * Template name: Campaign
 */

class Page_campaign extends MVC_controller {
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

    $lhs_menu_on = get_post_meta($post->ID, 'dw_lhs_menu_on', true) != "0" ? true : false;

    if ($lhs_menu_on) {
      $content_classes = 'col-lg-9 col-md-8 col-sm-12';
    }
    else {
      $content_classes =  'col-lg-12 col-md-12 col-sm-12';
    }

    return array(
      'page' => 'pages/campaign/main',
      'template_class' => 'campaign',
      'cache_timeout' => 60 * 60, /* 1 hour */
      'page_data' => array(
        'id' => $this->post_ID,
        'title' => get_the_title(),
        'thumbnail' => $thumbnail[0],
        'thumbnail_alt_text' => $alt_text,
        'content' => $content,
        'content_classes' => $content_classes,
        'skin' => get_post_meta($this->post_ID, 'dw_campaign_skin', true),
        'lhs_menu_on' => $lhs_menu_on,
      )
    );
  }
}
