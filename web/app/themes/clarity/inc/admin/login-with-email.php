<?php

/**
 * Allow login with email address instead of username
 */

remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
add_filter( 'authenticate', 'dw_email_login_authenticate', 20, 3 );

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
