<?php
/**
 * Custom registration page
 */
class Register extends MVC_controller {
  function __construct() {
    parent::__construct();
  }

  function main() {
    if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      $val = new Validation();

      $email = $_POST['email'];

      $val->is_filled('first_name', 'first name', 'Please enter first name');
      $val->is_filled('surname', 'surname', 'Please enter surname');
      $is_email_filled = $val->is_filled('email', 'email', 'Please enter email');
      $val->is_filled('display_name', 'display name', 'Please enter display name');

      if($is_email_filled) {
        if($val->is_valid_email('email', 'email', 'Please enter valid email')) {

          if(email_exists($email)) {
            $val->error('email', 'email', 'This email address is already in use');
          }
        }
      }

      if(!$val->has_errors()) {
        $user_id = wp_insert_user(array(
          'user_login' => $email,
          'user_email' => $email,
          'first_name' => $_POST['first_name'],
          'last_name' => $_POST['surname'],
          'display_name' => $_POST['display_name']
        ));

        wp_new_user_notification($user_id, null, 'both');
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
      'page' => 'pages/register/main',
      'template_class' => 'user-register',
      'cache_timeout' => 0 /* no cache */,
      'no_breadcrumbs' => true,
      'page_data' => array(
        'message' => $message,
        'message_type' => $message_type,
        'login_url' => site_url('/login/'),
        'user_email' => $_GET['email']
      )
    );
  }
}
