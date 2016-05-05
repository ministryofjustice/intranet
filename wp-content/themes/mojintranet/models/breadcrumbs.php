<?php if (!defined('ABSPATH')) die();

class Breadcrumbs_model extends MVC_model {
  function get_data() {
    wp_reset_query();

    $breadcrumbs = array();
    $id = get_the_ID();
    $type = get_post_type();
    $parents = get_post_ancestors($id);
    $home_page_id = Taggr::get_id('homepage');
    $landing_page_id = null;

    $parents = array_reverse($parents);
    array_push($parents, $id);

    if($type == 'news') {
      $landing_page_id = Taggr::get_id('news-landing');
    }
    elseif($type == 'post') {
      $landing_page_id = Taggr::get_id('blog-landing');
    }
    elseif($type == 'event') {
      $landing_page_id = Taggr::get_id('events-landing');
    }
    elseif($type == 'webchat') {
      $landing_page_id = Taggr::get_id('webchats-landing');
    }

    if($landing_page_id) array_unshift($parents, $landing_page_id);
    if($home_page_id && $home_page_id != $id) array_unshift($parents, $home_page_id);

    //convert the ids array to useful data
    foreach($parents as $page_id) {
      $breadcrumbs[] = array(
        'title' => get_the_title($page_id),
        'url' => get_permalink($page_id)
      );
    }

    $breadcrumbs[count($breadcrumbs) - 1]['last'] = true;

    return is_404() ? array() : $breadcrumbs;
  }
}
