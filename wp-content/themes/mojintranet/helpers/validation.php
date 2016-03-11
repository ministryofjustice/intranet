<?php if (!defined('ABSPATH')) die();

class Validation {
  private $errors = array();

  public function __construct() {
  }

  public function error($post_name, $field_name, $message) {
    $this->errors[] = array(
      'name' => $post_name,
      'field_name' => $field_name,
      'message' => $message
    );
  }

  public function is_filled($post_name, $field_name, $message = null) {
    $value = $this->_get_field($post_name);

    if(!$value) {
      $this->error($post_name, $field_name, $message);
      return false;
    }

    return true;
  }

  public function is_valid_email($post_name, $field_name, $message = null) {
    $value = $this->_get_field($post_name);

    if(!preg_match('/[^ ]+@[^ ]+/', $value)) {
      $this->error($post_name, $field_name, $message);
      return false;
    }

    return true;
  }

  public function has_errors() {
    return count($this->errors) > 0;
  }

  public function get_errors() {
    return $this->errors;
  }

  private function _get_field($post_name) {
    return $_POST[$post_name];
  }
}
