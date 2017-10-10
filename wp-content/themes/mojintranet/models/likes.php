<?php if (!defined('ABSPATH')) die();

class Likes_model extends MVC_model {
  public static $meta_key = 'dw_inc_likes';

  public function read($content_type, $post_id) {
    return array(
      'count' => (int) $this->get_like_count($content_type, $post_id)
    );
  }

  public function update($content_type,$post_id) {
    $count = $this->get_like_count($content_type,$post_id) + 1;
    $update_status = call_user_func("update_" . $content_type . "_meta", $post_id, $this::$meta_key, $count);
    return array(
      "count" => (int) $count,
      "update_status" => $update_status
    );
  }

  private function get_like_count($content_type, $post_id) {
    return call_user_func("get_" . $content_type . "_meta", $post_id, $this::$meta_key, true)?:0;
  }
}
