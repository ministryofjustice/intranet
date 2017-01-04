<?php if (!defined('ABSPATH')) die();

/**
 * The campaign hub template
 *
 * Template name: Campaign Hub
 */

class Page_campaign_landing extends MVC_controller {
  function main() {
    the_post();
    $this->view('layouts/default', $this->get_data());
  }

  private function get_data() {
    $post = get_post($this->post_id);
    $lhs_menu_on = get_post_meta($this->post_id, 'lhs_menu_on', true) != "0";

    $banner_id = get_post_meta($this->post_id, 'dw_page_banner', true);
    $banner_image = wp_get_attachment_image_src($banner_id, 'full');

    return [
      'page' => 'pages/campaign_landing/main',
      'template_class' => 'campaign-landing',
      'cache_timeout' => 60 * 60 * 24 /* 1 day */,
      'page_data' => [
        'id' => $this->post_id,
        'title' => get_the_title(),
        'excerpt' => $post->post_excerpt,
        'lhs_menu_on' => $lhs_menu_on,
        'banner_url' => $banner_image[0],
        'news_widget' => [
          'see_all_url' => '',
          'see_all_label' => '',
          'type' => 'campaign',
          'number_of_lists' => 1,
          'no_items_found_message' => 'No news found',
          'list_container_classes' => 'col-lg-12 col-md-12 col-sm-12',
          'skeleton_screen_count' => 4
        ],
        'events_widget' => [
          'see_all_url' => '',
          'see_all_label' => '',
          'type' => 'campaign',
          'no_items_found_message' => 'No events found',
          'skeleton_screen_count' => 2
        ],
        'posts_widget' => [
          'see_all_url' => '',
          'see_all_label' => '',
          'no_items_found_message' => 'No posts found',
          'type' => 'campaign',
          'skeleton_screen_count' => 2,
          'number_of_lists' => 2,
          'list_container_classes' => 'col-lg-6 col-md-12 col-sm-12'
        ]
      ]
    ];
  }
}
