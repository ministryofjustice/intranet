<?php if (!defined('ABSPATH')) die();

class Children_model extends MVC_model {
  private $post_types = array('page', 'webchat');

  public function get_all($page_id = 0, $order = 'asc') {
    $this->page_id = $page_id;
    $this->order = $order;

    $data = array(
      'title' => (string) get_the_title($page_id),
      'id' => (int) $page_id,
      'url' => (string) get_permalink($page_id),
      'total_results' => 0,
      'results' => array()
    );

    $children = $this->get_children();

    foreach($children->posts as $post) {
      $data['results'][] = $this->trim_child($post);
    }

    usort($data['results'], array($this,'sort_children'));

    if($order == 'desc') {
      $data['results'] = array_reverse($data['results']);
    }

    $data['total_results'] = count($data['results']);

    return $data;
  }

  private function get_children() {
    //get this page
    $top_page = new WP_Query(array(
      'p' => $this->page_id,
      'post_type' => $this->post_types
    ));
    $top_page->the_post();

    $children_args = array(
      'post_parent' => $this->page_id,
      'post_type' => $this->post_types,
      'posts_per_page' => -1
    );

    if(!$this->page_id) {
      $children_args['meta_key'] = 'is_top_level';
      $children_args['meta_value'] = 1;
    }

    return new WP_Query($children_args);
  }

  private function trim_child($post) {
    $id = $post->ID;
    setup_postdata(get_post($id));

    $grandchildren = new WP_Query(array(
      'post_type' => $this->post_types,
      'post_parent' => $id,
      'posts_per_page' => -1
    ));

    return array(
      'id' => $id,
      'title' => $this->trim_title(get_the_title($id)),
      'url' => get_the_permalink($id),
      'slug' => $post->post_name,
      'excerpt' => get_the_excerpt_by_id($id),
      'order' => $post->menu_order,
      'child_count' => $grandchildren->post_count,
      'is_external' => (boolean) get_post_meta($id, 'redirect_enabled', true),
      'status' => $post->post_status
    );
  }

  private function trim_title($title) {
    return preg_replace('/(.*:\s*)/', "", $title);
  }

  private function sort_children($a, $b) {
    if($a['menu_order'] > 0 && $b['menu_order'] > 0) {
      return $a['menu_order'] > $b['menu_order'] ? 1 : -1;
    }
    elseif($a['menu_order'] != $b['menu_order']) {
      return $a['menu_order'] > $b['menu_order'] ? -1 : 1;
    }

    return strnatcmp(strtolower($a['title']), strtolower($b['title']));
  }
}
