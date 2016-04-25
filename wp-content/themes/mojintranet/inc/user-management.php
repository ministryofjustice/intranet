<?php

// Tweak admin bar for non-admins
function hide_admin_bar_for_regular_users() {
  if (!current_user_can('agency-editor') && !current_user_can('editor') && !current_user_can('administrator')) {
    show_admin_bar(false);
  }
}
add_action( 'after_setup_theme', 'hide_admin_bar_for_regular_users' );

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
  $query = new WP_Query( 'pagename=login' );
  if ( $query->have_posts() ) {
    $login_page  = home_url( '/sign-in/' );
    $page_viewed = basename($_SERVER['REQUEST_URI']);

    if( $page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
        wp_redirect($login_page);
        exit;
    }
  }
}
add_action('init','dw_redirect_login_page');
