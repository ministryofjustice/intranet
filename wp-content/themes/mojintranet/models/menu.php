<?php if (!defined('ABSPATH')) die();

class Menu_model extends MVC_model {
  public function get_menu_items($params = array()) {
    $location = isset($params['location']) ? $params['location'] : 'hq-guidance-index';
    $with_children = isset($params['with_children']) ? $params['with_children'] : false;

    $locations = get_nav_menu_locations();
    $menu_items = wp_get_nav_menu_items($locations[$location]);

    if(!$menu_items) {
      $menu_items = array();
    }

    $organised_menu = $this->_build_menu_tree($menu_items, $with_children);

    return $organised_menu;
  }

  private function _build_menu_tree($data, $with_children) {
    $organised_menu = array();
    $count = 0;

    foreach($data as $item) {
      $count++;

      $item = array(
        'title' => $item->title,
        'ID' => $item->ID,
        'object_id' => (int) $item->object_id,
        'menu_item_parent' => $item->menu_item_parent,
        'url' => $item->url,
        'children' => array()
      );

      if($item['menu_item_parent']) {
        if($with_children) {
          $organised_menu[$item['menu_item_parent']]['children'][$item['ID']] = $item;
        }
      }
      else {
        $organised_menu[$item['ID']] = $item;
      }
    }

    return $organised_menu;
  }
}
