<?php

// Tweak admin bar for non-admins
function remove_admin_bar_links() {
  if(!current_user_can( 'manage_options' )) {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('updates');
    $wp_admin_bar->remove_menu('search');
    $wp_admin_bar->remove_menu('site-name');
    Debug::full($wp_admin_bar);
    
  }
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

function register_user_management_pages() {
  // Add &setup-pages=1 to any admin screen to add pages
  if($_GET['setup-pages']==1 && current_user_can( 'manage_options' )) {
    $pages_to_register = array(
      'login' => 'Login',
      'forgot-password' => 'Forgot Password',
      'register' => 'Register',
      'change-password' => 'Change Password',
      'profile' => 'User Profile'
    );

    foreach ($pages_to_register as $slug=>$title) {
      $query = new WP_Query( 'pagename=' . $slug );
      if ( ! $query->have_posts() ) {
        $post_array = array(
            'post_name' => $slug,
            'post_title' => $title,
            'post_status' => 'publish',
            'post_type' => 'page',
            'ping_status' => 'closed',
            'comment_status' => 'closed'
          );
        wp_insert_post($post_array);
      }
    }
  }
}
add_action('init','register_user_management_pages');

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
  $query = new WP_Query( 'pagename=login' );
  if ( $query->have_posts() ) {
    $login_page  = home_url( '/login/' );
    $page_viewed = basename($_SERVER['REQUEST_URI']);

    if( $page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
        wp_redirect($login_page);
        exit;
    }
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
  $query = new WP_Query( 'pagename=login' );
  if ( $query->have_posts() ) {
    $login_page  = home_url( '/login/' );
    $register_page  = home_url( '/register/' );
    if( $username == "" || $password == "" ) {
        wp_redirect( $login_page . "?status=empty" );
        exit;
    }
  }
}
add_filter( 'authenticate', 'dw_verify_username_password', 1, 3);

// Handles registration errors
function dw_registration_errors($errors, $sanitized_user_login, $user_email) {
  $user_firstname = $_POST['user_firstname'];
  $user_surname = $_POST['user_surname'];
  $user_email = $_POST['user_email'];
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
  if(!$user_firstname || !$user_surname) {
    wp_redirect($register_page . "?status=noname&email=".$user_email);
    $errors->add('noname','First name/surname incomplete');
    exit;
  }
  return $errors;
}
add_filter('registration_errors','dw_registration_errors',10,3);

function save_registration_metadata($user_id) {
  $user_firstname = $_POST['user_firstname'];
  $user_surname = $_POST['user_surname'];
  $user_displayname = $_POST['user_displayname']?:($user_firstname . " " . $user_surname);
  wp_update_user(array(
    'ID' => $user_id,
    'display_name' => $user_displayname,
    'nickname' => $user_displayname,
    'first_name' => $user_firstname,
    'last_name' => $user_surname
  ));
}
add_action('register_new_user','save_registration_metadata');

// Handles password reset errors
function dw_lostpassword_errors($errors) {
  $lostpassword_page  = home_url( '/forgot-password/' );
  $errors_array = $errors->errors;
  if(isset($errors_array['empty_username'])) {
    wp_redirect($lostpassword_page . "?status=empty");
    exit;
  }
  if(isset($errors_array['invalid_email'])) {
    wp_redirect($lostpassword_page . "?status=invalid");
    exit;
  }
  return $errors;
}
add_filter('lostpassword_post','dw_lostpassword_errors',10);

// Redirect to custom password reset page

function dw_password_reset_redirect() {
  if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
    // Verify key / login combo
    $user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
    if ( ! $user || is_wp_error( $user ) ) {
      if ( $user && $user->get_error_code() === 'expired_key' ) {
        wp_redirect( home_url( 'login?status=expiredkey' ) );
      } else {
        wp_redirect( home_url( 'login?status=invalidkey' ) );
      }
      exit;
    }

    $redirect_url = home_url( 'change-password' );
    $redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
    $redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );

    wp_redirect( $redirect_url );
    exit;
  }
}
add_action( 'login_form_rp','dw_password_reset_redirect', 1 );
add_action( 'login_form_resetpass','dw_password_reset_redirect', 1 );

//Resets the user's password if the password reset form was submitted.
function dw_password_reset() {
  if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
    $rp_key = $_REQUEST['rp_key'];
    $rp_login = $_REQUEST['rp_login'];

    $user = check_password_reset_key( $rp_key, $rp_login );

    if ( ! $user || is_wp_error( $user ) ) {
      if ( $user && $user->get_error_code() === 'expired_key' ) {
        wp_redirect( home_url( 'login?status=expiredkey' ) );
      } else {
        wp_redirect( home_url( 'login?status=invalidkey' ) );
      }
    exit;
    }

    if ( isset( $_POST['pass1'] ) ) {
      if ( $_POST['pass1'] != $_POST['pass2'] ) {
        // Passwords don't match
        $redirect_url = home_url( 'change-password' );

        $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
        $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
        $redirect_url = add_query_arg( 'status', 'password_mismatch', $redirect_url );

        wp_redirect( $redirect_url );
        exit;
      }

      if ( empty( $_POST['pass1'] ) ) {
        // Password is empty
        $redirect_url = home_url( 'change-password' );

        $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
        $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
        $redirect_url = add_query_arg( 'status', 'password_empty', $redirect_url );

        wp_redirect( $redirect_url );
        exit;
      }

      // Parameter checks OK, reset password
      reset_password( $user, $_POST['pass1'] );
      wp_redirect( home_url( 'login?status=password-changed' ) );
    } else {
      echo "Invalid request.";
    }

    exit;
  }
}
add_action( 'login_form_rp','dw_password_reset');
add_action( 'login_form_resetpass','dw_password_reset');