<?php if (!defined('ABSPATH')) die();

class Password_tpl_model extends MVC_model {
  public function set() {
    return array(
      'page_title_text' => 'Create password',
      'cta_text' => 'Create',
      'enter_password_text' => 'Password',
      'reenter_password_text' => 'Re-enter password',
      'confirmation_message' => 'Your account has been activated.'
    );
  }

  public function reset() {
    return array(
      'page_title_text' => 'Reset password',
      'cta_text' => 'Reset',
      'enter_password_text' => 'Enter your new password',
      'reenter_password_text' => 'Re-enter your new password',
      'confirmation_message' => 'You\'ve changed your password successfully.'
    );
  }
}
