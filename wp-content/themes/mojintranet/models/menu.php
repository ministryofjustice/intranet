<?php if (!defined('ABSPATH')) die();

class Menu_model extends MVC_model {
  public function get_menu_items($params = array()) {
    $location = isset($params['location']) ? $params['location'] : 'hq-guidance-index';
    $depth_limit = isset($params['depth_limit']) ? $params['depth_limit'] : 0;

    $locations = get_nav_menu_locations();
    $menu_items = wp_get_nav_menu_items($locations[$location]);

    if(!$menu_items) {
      $menu_items = array();
    }

    $organised_menu_items = $this->_build_menu_tree_recursive($menu_items, $depth_limit);

    return array(
      'results' => $organised_menu_items
    );
  }

  private function _build_menu_tree_recursive($data, $depth_limit = 0, $parent_id = 0, $level = 1) {
    $clean_data = array();

    foreach($data as $key => $item) {
      if($item->menu_item_parent == $parent_id) {
        $clean_item = array(
          'title' => $item->title,
          'ID' => $item->ID,
          'object_id' => (int) $item->object_id,
          'url' => $item->url,
          'classes' => implode($item->classes, ' '),
          'children' => array()
        );

        if(!$depth_limit || $level < $depth_limit) {
          $clean_item['children'] = $this->_build_menu_tree_recursive($data, $depth_limit, $item->ID, $level + 1);
        }

        $clean_data[] = $clean_item;

        unset($data[$key]);
      }
    }

    return $clean_data;
  }
}
