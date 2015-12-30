<?php
/**
 * Custom registration page
 */
class Page_register extends MVC_controller {
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
    if ( $status === "success" ) {
      $message = 'Please check your mailbox for further instructions.';
      $message_type = "info";
    } elseif ( $status === "empty" ) {
      $message = 'Email address must not be empty.';
      $message_type = "error";
    } elseif ( $status === "exists" ) {
      $message = 'Email address has already been registered. Please check your email or contact the Intranet team.';
      $message_type = "error";
    }

    return array(
      'page' => 'pages/register/main',
      'cache_timeout' => 0 /* no cache */,
      'no_breadcrumbs' => true,
      'page_data' => array(
        'message' => $message,
        'message_type' => $message_type
      )
    );
  }
}