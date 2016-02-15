<?php if (!defined('ABSPATH')) die();

class Password_tpl_model extends MVC_model {
  public function set() {
    return array(
      'page_title_text' => 'Create password',
      'cta_text' => 'Create'
    );
  }

  public function reset() {
    return array(
      'page_title_text' => 'Reset password',
      'cta_text' => 'Reset'
    );
  }
}
