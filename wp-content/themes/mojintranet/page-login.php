<?php
/**
 * Custom login page
 */
class Page_login extends MVC_controller {
  function __construct() {
    parent::__construct();
  }

  function main() {
    if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in
    if(have_posts()) the_post();
    $this->view('layouts/default', $this->get_data());
  }

  private function get_data() {
    // Set error message (if any)
    $login_error  = (isset($_GET['login']) ) ? $_GET['login'] : 0;
    if ( $login_error === "failed" ) {
      $error_message = 'Invalid email address and/or password.';
    } elseif ( $login_error === "empty" ) {
      $error_message = 'Email address and/or password is empty.';
    } elseif ( $login_error === "false" ) {
      $error_message = 'You are logged out.';
    }

    return array(
      'page' => 'pages/login/main',
      'cache_timeout' => 0 /* no cache */,
      'no_breadcrumbs' => true,
      'page_data' => array(
        'login_args' => array(
          'redirect' => site_url('/'),
          'remember' => false,
          'label_username' => "Email address"
        ),
        'error_message' => $error_message
      )
    );
  }
}