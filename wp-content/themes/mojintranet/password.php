<?php
/**
 * Password change template
 */
class Password extends MVC_controller {
  function __construct($param_string) {
    parent::__construct($param_string);
  }

  function main() {
    $this->set();
  }

  function set() {
    //if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in
    $this->view('layouts/default', $this->_get_data());
  }

  function reset() {
    $this->view('layouts/default', $this->_get_data());
  }

  private function _get_data() {
    return array(
      'page' => 'pages/change_password/main',
      'cache_timeout' => 0 /* no cache */,
      'no_breadcrumbs' => true,
      'page_data' => array(
        'message' => $message,
        'message_type' => $message_type,
        'login_url' => site_url('/login/'),
        'hide_form' => $hide_form,
        'page_title' => 'Choose password',
        'login' => $login,
        'key' => $key
      )
    );
  }
}
