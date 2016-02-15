<?php
/**
 * Password change template
 */
class Password extends MVC_controller {
  function __construct($param_string) {
    parent::__construct($param_string);

    $this->model('password_tpl');
  }

  function main() {
    $this->reset();
  }

  function set() {
    if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in
    $this->view('layouts/default', $this->_get_data('set'));
  }

  function reset() {
    if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in
    $this->view('layouts/default', $this->_get_data('reset'));
  }

  private function _get_data($type) {
    return array(
      'page' => 'pages/change_password/main',
      'cache_timeout' => 0 /* no cache */,
      'no_breadcrumbs' => true,
      'page_data' => array(
        'tpl' => $this->model->password_tpl->$type()
      )
    );
  }
}
