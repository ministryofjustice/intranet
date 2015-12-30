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
    $status  = (isset($_GET['status']) ) ? $_GET['status'] : 0;
    if ( $status === "failed" ) {
      $message = 'Invalid email address and/or password.';
      $message_type = "error";
    } elseif ( $status === "empty" ) {
      $message = 'Email address and/or password is empty.';
      $message_type = "error";
    } elseif ( $status === "false" ) {
      $message = 'You are logged out.';
      $message_type = "info";
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
        'message' => $message,
        'message_type' => $message_type
      )
    );
  }
}