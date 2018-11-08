<?php if (!defined('ABSPATH')) die();

class Password_tpl_model extends MVC_model {
  public function set() {
    return array(
      'page_title_text' => 'Create a password',
      'cta_text' => 'Create password',
      'enter_password_text' => 'Create your password',
      'reenter_password_text' => 'Re-enter your password',
      'confirmation_title_text' => 'Account created',
      'confirmation_message_text' => 'Your account and password have successfully been created. Please sign in again.'
    );
  }

  public function reset() {
    return array(
      'page_title_text' => 'Reset password',
      'cta_text' => 'Reset password',
      'enter_password_text' => 'Enter your new password',
      'reenter_password_text' => 'Re-enter your new password',
      'confirmation_title_text' => 'Password changed',
      'confirmation_message_text' => 'Your password has been successfully changed. Please sign in again.'
    );
  }
}
