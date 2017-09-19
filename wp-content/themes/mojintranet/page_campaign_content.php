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

    $this_id = $post->ID;

    $banner_id = get_post_meta($this->post_ID, 'dw_page_banner', true);
    $banner_url = get_post_meta($this->post_ID, 'dw_banner_url', true);
    $banner_image = wp_get_attachment_image_src($banner_id, 'full');

    $colour_hex = get_post_meta($this->post_ID, 'dw_campaign_colour', true);

    $lhs_menu_on = get_post_meta($this->post_ID, 'dw_lhs_menu_on', true) != "0" ? true : false;

    if ($lhs_menu_on) {
      $content_classes = 'col-lg-9 col-md-8 col-sm-12';
    }
    else {
      $content_classes =  'col-lg-9 col-md-12 col-sm-12';
    }

    $this->add_global_view_var('commenting_policy_url', site_url('/commenting-policy/'));
    $this->add_global_view_var('comments_open', (boolean) comments_open($this_id));
    $this->add_global_view_var('comments_on', (boolean) get_post_meta($this_id, 'dw_comments_on', true));
    $this->add_global_view_var('logout_url', wp_logout_url($_SERVER['REQUEST_URI']));

    $likes = $this->get_likes_from_api($this_id);

    return array(
      'page' => 'pages/campaign_content/main',
      'template_class' => 'campaign-content',
      'cache_timeout' => 60 * 60, /* 1 hour */
      'page_data' => array(
        'id' => $this->post_ID,
        'title' => get_the_title(),
        'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'content' => $content,
        'content_classes' => $content_classes,
        'lhs_menu_on' => $lhs_menu_on,
        'banner_image_url' => $banner_image[0],
        'banner_url' => $banner_url,
        'media_bar' => [
          'share_email_body' => "Hi there,\n\nI thought you might be interested in this page I've found on the MoJ intranet:\n",
          'likes_count' => $likes['count']
          ],
        'style_data' => [
          'campaign_colour' => $colour_hex
        ]
      )
    );
  }
  private function get_likes_from_api($post_id) {
    return $this->model->likes->read('post', $post_id);
  }
}
