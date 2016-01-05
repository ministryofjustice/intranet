<?php
/**
 * Password change template
 */
class Page_change_password extends MVC_controller {
  function __construct() {
    parent::__construct();
  }

  function main() {
    if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in
    if(have_posts()) the_post();
    $this->view('layouts/default', $this->get_data());
  }

  private function get_data() {
    $login = $_GET['login'];
    $key = $_GET['key'];
    $user = get_user_by( 'login', $login );
    if($user->user_activation_key) {
      $page_title = "Set Password";
    } else {
      $page_title = "Reset Password";
    }
    $hide_form = false;
    // Set error message (if any)
    if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
      $status  = (isset($_GET['status']) ) ? $_GET['status'] : 0;
      if ( $status === "success" ) {
        $message = 'Please check your mailbox for further instructions.';
        $message_type = "info";
      } elseif ( $status === "password_mismatch" ) {
        $message = 'The passwords do not match. Please try again.';
        $message_type = "error";
      } elseif ( $status === "password_empty" ) {
        $message = 'The passwords must not be empty. Please try again.';
        $message_type = "error";
      }
    } else {
      $message = 'Invalid password reset link.';
      $message_type = "error";
      $hide_form = true;
    }

    return array(
      'page' => 'pages/change_password/main',
      'cache_timeout' => 0 /* no cache */,
      'no_breadcrumbs' => true,
      'page_data' => array(
        'message' => $message,
        'message_type' => $message_type,
        'login_url' => site_url('/login/'),
        'hide_form' => $hide_form,
        'page_title' => $page_title,
        'login' => $login,
        'key' => $key
      )
    );
  }
}