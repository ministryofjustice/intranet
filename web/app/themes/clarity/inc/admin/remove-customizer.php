<?php

/**
 *
 * Becuse we are using ACF for the homepage we have removed customizer
 */
add_action( 'wp_before_admin_bar_render', 'remove_customizer' );

function remove_customizer() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu( 'customize' );
}

add_action( 'admin_menu', 'remove_customizer_appearance_menu' );

function remove_customizer_appearance_menu() {
	global $submenu;
	unset( $submenu['themes.php'][6] ); // Customize
}
