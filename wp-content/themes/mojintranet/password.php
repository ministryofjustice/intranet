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
    elseif($_SERVER['REQUEST_METHOD'] == 'POST') $this->_process_reset_form('set');
    elseif($this->_is_expired()) $this->_expired_view('set');
    else $this->_reset_form_view('set');
  }

  function reset() {
    if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in
    elseif($_SERVER['REQUEST_METHOD'] == 'POST') $this->_process_reset_form('reset');
    elseif($this->_is_expired()) $this->_expired_view('reset');
    else $this->_reset_form_view('reset');
  }

  function forgotten() {
    if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in
    elseif($_SERVER['REQUEST_METHOD'] == 'POST') $this->_process_forgotten_form();
    else $this->_forgotten_form_view();
  }

  private function _process_reset_form($type) {
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
      $updated = $this->model->user->update($user->ID, array(
        'user_activation_key' => null,
        'user_pass' => wp_hash_password($password)
      ));

      //send email to user
      $data = array(
        'name' => $user->display_name,
        'login_url' => site_url('/sign-in')
      );

      //Disabling confirmation emails for now
      //if($type == 'set') {
      //  $message = $this->view('email/account_activated', $data, true);
      //
      //  html_mail($login, 'MoJ Intranet - Account activated', $message);
      //}
      //else {
      //  $message = $this->view('email/password_reset', $data, true);
      //
      //  html_mail($login, 'MoJ Intranet - Password successfully reset', $message);
      //}
    }

    $this->output_json(array(
      'success' => !$val->has_errors(),
      'validation' => $val->get_errors()
    ));
  }

  private function _process_forgotten_form() {
    $val = new Validation();

    $email = $_POST['email'];

    $val->is_filled('email', 'email', 'Please enter email');

    if(!$val->has_errors()) {
      if(!$this->model->user->is_gov_email($email)) {
        $val->error('email', 'email', 'You need to use an MoJ email address');
      }
    }

    if(!$val->has_errors()) {
      $user = get_user_by('email', $email);

      if($user) {
        $key = $this->model->user->set_activation_key($user->ID);

        //send email to user
        $data = array(
          'name' => $user->display_name,
          'reset_password_url' => network_site_url("/password/reset/?key=".$key."&login=" . rawurlencode($email), 'login')
        );

        $message = $this->view('email/password_reset_requested', $data, true);

        html_mail($email, 'You\'ve requested a password change', $message);
      }
    }

    $this->output_json(array(
      'success' => !$val->has_errors(),
      'validation' => $val->get_errors()
    ));
  }

  private function _is_expired() {
    $email = $_POST['login'] ?: $_GET['login'];
    $user = get_user_by('email', $email);
    $key = $_POST['key'] ?: $_GET['key'];
    $user = check_password_reset_key($key, $user->data->user_login);

    return is_array($user->errors);
  }

  private function _reset_form_view($type) {
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

  private function _forgotten_form_view() {
    $data = array(
      'page' => 'pages/forgot_password/main',
      'template_class' => 'user-forgot-password',
      'cache_timeout' => 0 /* no cache */,
      'no_breadcrumbs' => true,
      'page_data' => array(
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
