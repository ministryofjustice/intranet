<?php

/**
 *
 * Enqueue Clarity scripts and styles.
 *
 */

add_action( 'wp_enqueue_scripts', 'enqueue_clarity_scripts', 99 );

function enqueue_clarity_scripts() {
	// CSS
	wp_register_style( 'core-css', get_stylesheet_directory_uri() . '/assets/css/core.min.css', array(), '1.2.3', 'all' );
	wp_enqueue_style( 'core-css' );

	wp_register_style( 'ie-css', get_stylesheet_directory_uri() . '/assets/css/ie.min.css', array(), null, 'screen' );
	wp_enqueue_style( 'ie-css' );
	wp_style_add_data( 'ie-css', 'conditional', 'IE 7' );

	wp_register_style( 'ie8-css', get_stylesheet_directory_uri() . '/assets/css/ie8.min.css', array(), null, 'screen' );
	wp_enqueue_style( 'ie8-css' );
	wp_style_add_data( 'ie8-css', 'conditional', 'IE 8' );

	wp_register_style( 'print-css', get_stylesheet_directory_uri() . '/assets/css/print.min.css', array(), null, 'print' );
	wp_enqueue_style( 'print-css' );

	// JS and jQuery
	wp_enqueue_script( 'core-js', get_stylesheet_directory_uri() . '/assets/js/core.min.js', array( 'jquery' ), '1.1.1', true );
	wp_localize_script( 'core-js', 'myAjax', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );

	// Third party vendor scripts
	wp_deregister_script( 'jquery' ); // This removes jquery shipped with WP so that we can add our own.
	wp_register_script( 'jquery', get_stylesheet_directory_uri() . '/assets/vendors/jquery.min.js', array(), '1.12.4' );
	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'popup', get_stylesheet_directory_uri() . '/assets/vendors/magnific-popup.js', array( 'jquery' ) );

	wp_enqueue_script( 'html5shiv', get_stylesheet_directory_uri() . '/assets/vendors/ie8-js-html5shiv.js' );
	wp_style_add_data( 'html5shiv', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'respond', get_stylesheet_directory_uri() . '/assets/vendors/respond.min.js' );
	wp_style_add_data( 'respond', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'selectivizr', get_stylesheet_directory_uri() . '/assets/vendors/selectivizr-min.js' );
}

/**
 *
 * Enqueued backend admin CSS and JS
 *
 */
add_action( 'admin_enqueue_scripts', 'clarity_admin_enqueue' );

function clarity_admin_enqueue( $hook ) {

	 // Warning message to editors when they don't enter a page title
	 if ( $hook == 'post-new.php' || $hook == 'post.php' ) :
		 wp_enqueue_script( 'force_title_script', get_stylesheet_directory_uri() . '/inc/admin/js/force-title.js', array(), null, false  );
	 endif;

	 wp_enqueue_script( 'network-connectivity', get_stylesheet_directory_uri() . '/inc/admin/js/network-connectivity.js', array(), '0.2.0', false );

}
