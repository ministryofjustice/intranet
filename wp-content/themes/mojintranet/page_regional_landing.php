<?php if (!defined('ABSPATH')) die();

/**
 * Controller for regional pages.
 *
 */

class Page_regional_landing extends MVC_controller {
  function main(){
    $this->model('my_moj');

    while(have_posts()){
      the_post();

      $this->post_ID = get_the_ID();

      $this->view('layouts/default', $this->get_data());
    }
  }

  function get_data(){
    $post = get_post($this->post_ID);
    $agencies = get_the_terms($this->post_ID, 'agency');
    $list_of_agencies = [];

    foreach ($agencies as $agency) {
      $list_of_agencies[] = $agency->name;
    }

    ob_start();
    the_content();
    $content = ob_get_clean();

    return [
      'page' => 'pages/regional_landing/main',
      'template_class' => 'regional-landing',
      'cache_timeout' => 60 * 60, /* 1 hour */
      'page_data' => [
        'id' => $this->post_ID,
        'title' => get_the_title(),
        'agencies' => implode(', ', $list_of_agencies),
        'region' => get_the_terms($this->post_ID, 'region')[0]->slug,
        'last_updated' => date("j F Y", strtotime(get_the_modified_date())),
        'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'content' => $content,
        'hide_page_details' => (boolean) get_post_meta($this->post_ID, 'dw_hide_page_details', true),
        'news_widget' => [
          'see_all_url' => '',
          'see_all_label' => 'See all updates',
          'type' => 'regional',
          'number_of_lists' => 1,
          'no_items_found_message' => 'No updates found',
          'list_container_classes' => 'col-lg-12 col-md-12 col-sm-12',
          'skeleton_screen_count' => 2
        ],
        'events_widget' => [
          'see_all_url' => '',
          'see_all_label' => 'See all events',
          'no_items_found_message' => 'No events found',
          'type' => 'regional',
          'skeleton_screen_count' => 2
        ],
      ]
    ];
  }
}
