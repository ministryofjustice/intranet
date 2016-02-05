<?php if (!defined('ABSPATH')) die();

class Likes_model extends MVC_model {
  public static $meta_key = 'dw_inc_likes';

  public function read($post_id) {
    return array(
      'count' => (int) $this->get_like_count($post_id)
    );
  }

  public function update($post_id, $nonce) {
    if(wp_verify_nonce( $nonce, $this::$meta_key )) {
      $count = $this->get_like_count($post_id)+1;
      $update_status = update_post_meta( $post_id, $this::$meta_key, $count );
      return array(
        "count" => (int) $count,
        "nonce" => $nonce
      );
    } else {
      return false;
    }
  }

  private function get_like_count($post_id) {
    return get_post_meta( $post_id, $this::$meta_key, true )?:0;
  }
}
