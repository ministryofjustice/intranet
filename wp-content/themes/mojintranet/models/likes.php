<?php if (!defined('ABSPATH')) die();

class Likes_model extends MVC_model {
  public function read($post_id) {
    return array(
      'hello' => 'read'
    );
  }

  public function update($post_id) {
    return array(
      'hello' => 'update'
    );
  }
}
