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
    if(have_posts()) the_post();
    $this->view('layouts/default', $this->get_data());
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
