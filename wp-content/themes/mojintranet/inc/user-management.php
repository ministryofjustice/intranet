<?php

// Prevent /login from redirecting to wp-login.php
function dw_prevent_admin_redirect() {
  remove_action('template_redirect', 'wp_redirect_admin_locations', 1000);
}
add_action('init','dw_prevent_admin_redirect');

// Redirect to homepage when logging out
add_action('wp_logout',create_function('','wp_redirect(home_url());exit();'));

// Allow login with email address instead of username
function dw_email_login_authenticate( $user, $username, $password ) {
  if ( is_a( $user, 'WP_User' ) )
    return $user;

  if ( !empty( $username ) ) {
    $username = str_replace( '&', '&amp;', stripslashes( $username ) );
    $user = get_user_by( 'email', $username );
    if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status )
      $username = $user->user_login;
  }

  return wp_authenticate_username_password( null, $username, $password );
}
remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
add_filter( 'authenticate', 'dw_email_login_authenticate', 20, 3 );

// Redirect away from standard WordPress login page
function dw_redirect_login_page() {
    $login_page  = home_url( '/login/' );
    $page_viewed = basename($_SERVER['REQUEST_URI']);
 
    if( $page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
        wp_redirect($login_page);
        exit;
    }
}
add_action('init','dw_redirect_login_page');

// Handle failed login
function dw_login_failed() {
    $login_page  = home_url( '/login/' );
    wp_redirect( $login_page . '?login=failed' );
    exit;
}
add_action( 'wp_login_failed', 'dw_login_failed' );

// Handle empty username (email) or password
function dw_verify_username_password( $user, $username, $password ) {
    $login_page  = home_url( '/login/' );
    $register_page  = home_url( '/register/' );
    if( $username == "" || $password == "" ) {
        wp_redirect( $login_page . "?status=empty" );
        exit;
    }
}
add_filter( 'authenticate', 'dw_verify_username_password', 1, 3);

// Handles registration errors
function dw_registration_errors($errors, $sanitized_user_login, $user_email) {
  $register_page  = home_url( '/register/' );
  $errors_array = $errors->errors;
  if(isset($errors_array['empty_email'])) {
    wp_redirect($register_page . "?status=empty");
    exit;
  }
  if(isset($errors_array['email_exists'])) {
    wp_redirect($register_page . "?status=exists");
    exit;
  }
  return $errors;
}
add_filter('registration_errors','dw_registration_errors',10,3);