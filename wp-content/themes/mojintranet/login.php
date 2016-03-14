<?php if (!defined('ABSPATH')) die();

/**
 * Custom login page
 */
class Login extends MVC_controller {
  function __construct() {
    parent::__construct();
  }

  function main() {
    if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      $val = new Validation();

      $is_email_filled = $val->is_filled('password', 'password', 'Please enter password');
      $is_password_filled = $val->is_filled('email', 'email', 'Please enter email');

      if($is_email_filled) {
        if($val->is_valid_email('email', 'email', 'Please enter valid email')) {
          $email = $_POST['email'];
          $password = $_POST['password'];

          $user = get_user_by('email', $email);
          if(!wp_check_password($password, $user->data->user_pass, $user->ID)) {
            $val->error('password', 'password', 'Email and password don\'t match');
          }
        }
      }

      if(!$val->has_errors()) {
        wp_signon(array(
          'user_login' => $user->data->user_login,
          'user_password' => $password
        ));
      }

      $this->output_json(array(
        'success' => !$val->has_errors(),
        'data' => $val->get_errors()
      ));
    }
    else {
      if(have_posts()) the_post();
      $this->view('layouts/default', $this->get_data());
    }
  }

  private function get_data() {
    return array(
      'page' => 'pages/login/main',
      'template_class' => 'user-login',
      'cache_timeout' => 0 /* no cache */,
      'no_breadcrumbs' => true,
      'page_data' => array(
        'register_url' => site_url('/register/'),
        'forgot_password_url' => site_url('/forgot-password/')
      )
    );
  }
}
