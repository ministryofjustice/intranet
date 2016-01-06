<?php if (!defined('ABSPATH')) die();

class Children_model extends MVC_model {
  private $post_types;

  public function __construct() {
    $this->post_types = array('page', 'webchat');
  }

  public function get_all($page_id = 0, $order_by = 'menu_order title', $order = 'asc') {
    $this->page_id = $page_id;
    $this->order_by = $order_by;
    $this->order = $order;

    $data = array(
      'results' => array()
    );

    $children = $this->get_children();
    //Debug::full($children->posts);

    //die();

    foreach($children->posts as $post) {
      //Debug::full($post);
      $data['results'][] = $this->trim_child($post);
    }

    usort($data['results'], array($this,'sort_children'));

    /*** ***/

    //$x = array(
    //  array('title' => 'bbb', 'menu_order' => 0),
    //  array('title' => 'aaa', 'menu_order' => 0),
    //  array('title' => '1', 'menu_order' => 0),
    //  array('title' => '10', 'menu_order' => 0),
    //  array('title' => '2', 'menu_order' => 0),
    //  array('title' => 'ccc', 'menu_order' => 2),
    //  array('title' => 'ddd', 'menu_order' => 1),
    //  array('title' => '3', 'menu_order' => 5),
    //  array('title' => '30', 'menu_order' => 4),
    //  array('title' => '4', 'menu_order' => 3)
    //);
    //
    //usort($x, array($this, 'sort_children'));
    //
    //Debug::full($x); die();

    /*** ***/

    Debug::full($data); die();

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
      'posts_per_page' => -1,
      'orderby' => $this->order_by,
      'order' => $this->order
    );

    if(!$this->page_id) {
      $children_args['meta_key'] = 'is_top_level';
      $children_args['meta_value'] = 1;
    }

    return new WP_Query($children_args);
  }

  private function trim_child($post) {
    $id = $post->ID;
    the_post($id);

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
      'excerpt' => get_the_excerpt(),
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
