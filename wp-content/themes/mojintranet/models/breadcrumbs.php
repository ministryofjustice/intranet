<?php if (!defined('ABSPATH')) die();

class Breadcrumbs_model extends MVC_model {
  function get_data() {
    wp_reset_query();

    $breadcrumbs = [];
    $id = get_the_ID();
    $ancestor_ids = $this->model->hierarchy->get_ancestor_ids($id);
    $homepage_id = Taggr::get_id('homepage');

    if ($ancestor_ids[0] != $homepage_id) {
      array_unshift($ancestor_ids, $homepage_id);
    }

    //convert the ids to useful data
    foreach ($ancestor_ids as $post_id) {
      $breadcrumbs[] = [
        'title' => get_the_title($post_id),
        'url' => get_permalink($post_id),
        'last' => false
      ];
    }

    $breadcrumbs[count($breadcrumbs) - 1]['last'] = true;

    return is_404() ? [] : $breadcrumbs;
  }
}
