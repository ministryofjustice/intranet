<?php
/**
 * Password change template
 */
class Password extends MVC_controller {
  function __construct($param_string) {
    parent::__construct($param_string);

    $this->model('user');
    $this->model('password_tpl');
  }

  function main() {
    $this->reset();
  }

  function set() {
    if(is_user_logged_in()) wp_redirect('/');
    elseif($_SERVER['REQUEST_METHOD'] == 'POST') $this->_process_form();
    elseif($this->_is_expired()) $this->_expired_view('set');
    else $this->_form_view('set');
  }

  function reset() {
    if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in
    elseif($_SERVER['REQUEST_METHOD'] == 'POST') $this->_process_form();
    elseif($this->_is_expired()) $this->_expired_view('reset');
    else $this->_form_view('reset');
  }

  private function _process_form() {
    $val = new Validation();

    $password = $_POST['password'];
    $reenter_password = $_POST['reenter_password'];
    $login = $_POST['login'];
    $key = $_POST['key'];
    $user = check_password_reset_key($key, $login);

    //check if the account has the key set
    if(is_array($user->errors)) {
      $val->general_error('invalid_key', 'Invalid key');
    }

    if(!$val->has_errors()) {
      if($val->is_filled('password', 'password', 'Please enter password')) {
        if(strlen($password) < 8) {
          $val->error('password', 'password', 'Password must be at least 8 characters long');
        }
        elseif($password != $reenter_password) {
          $val->error('password', 'password', 'Passwords don\'t match');
        }
      }
    }

    if(!$val->has_errors()) {
      $this->model->user->update($user->ID, array(
        'user_activation_key' => null,
        'user_pass' => wp_hash_password($password)
      ));

      //Debug::full($user); die;
      //
      ////send email to user
      //$data = array(
      //  'name' => $login
      //);
      //
      //$message = $this->view('email/password', $data, true);
      //
      //html_mail($email, 'Subject', $message);
    }

    $this->output_json(array(
      'success' => !$val->has_errors(),
      'validation' => $val->get_errors()
    ));
  }

  private function _is_expired() {
    $login = $_POST['login'] ?: $_GET['login'];
    $key = $_POST['key'] ?: $_GET['key'];
    $user = check_password_reset_key($key, $login);

    return is_array($user->errors);
  }

  private function _form_view($type) {
    $data = array(
      'page' => 'pages/change_password/main',
      'template_class' => 'user-password',
      'cache_timeout' => 0 /* no cache */,
      'no_breadcrumbs' => true,
      'page_data' => array(
        'tpl' => $this->model->password_tpl->$type()
      )
    );

    $this->view('layouts/default', $data);
  }

  private function _expired_view($type) {
    $data = array(
      'page' => 'pages/change_password/expired',
      'template_class' => 'user-password',
      'cache_timeout' => 0 /* no cache */,
      'no_breadcrumbs' => true,
      'page_data' => array(
        'tpl' => $this->model->password_tpl->$type()
      )
    );

    $this->view('layouts/default', $data);
  }
}
