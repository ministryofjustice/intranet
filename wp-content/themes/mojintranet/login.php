<?php
/**
 * Custom login page
 */
class Login extends MVC_controller {
  function __construct() {
    parent::__construct();
  }

  function main() {
    if(have_posts()) the_post();
    $this->view('layouts/default', $this->get_data());
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
