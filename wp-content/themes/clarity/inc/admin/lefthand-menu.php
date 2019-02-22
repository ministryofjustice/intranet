<?php

use MOJ\Intranet;

/**
 * Activates the 'menu_order' filter and then hooks into 'menu_order'
 */
add_filter(
	'custom_menu_order',
	function() {
		return true;
	}
);
add_filter( 'menu_order', 'my_new_admin_menu_order' );
/**
 * Filters WordPress' default menu order
 */
function my_new_admin_menu_order( $menu_order ) {

	$new_positions = array(
		'index.php'                          => 0,
		'admin.php?page=header-settings'     => 1,
		'admin.php?page=homepage-settings'   => 2,
		'edit.php?post_type=news'            => 3,
		'edit.php?post_type=page'            => 4,
		'edit.php'                           => 5,
		'edit.php?post_type=event'           => 6,
		'edit.php?post_type=webchat'         => 7,
		'edit-comments.php'                  => 8,
		'upload.php'                         => 9,
		'edit.php?post_type=document'        => 10,
		'edit.php?post_type=poll'            => 11,
		'themes.php'                         => 12,
		'plugins.php'                        => 13,
		'users.php'                          => 14,
		'tools.php'                          => 15,
		'options-general.php'                => 16,
		'edit.php?post_type=acf-field-group' => 17,
		'edit.php?post_type=team_news'       => 18,
	);

	// helper function to move an element inside an array
	function move_element( &$array, $a, $b ) {
		$out = array_splice( $array, $a, 1 );
		array_splice( $array, $b, 0, $out );
	}
	// traverse through the new positions and move
	// the items if found in the original menu_positions
	foreach ( $new_positions as $value => $new_index ) {
		if ( $current_index = array_search( $value, $menu_order ) ) {
			move_element( $menu_order, $current_index, $new_index );
		}
	}
	return $menu_order;
};

add_action( 'admin_menu', 'remove_regions_from_nonhmcts_users' );

function remove_regions_from_nonhmcts_users() {
	$context = Agency_Context::get_agency_context();

	if ( $context != 'hmcts' ) {
		remove_menu_page( 'edit.php?post_type=regional_news' );
		remove_menu_page( 'edit.php?post_type=regional_page' );
	}
}

add_action( 'admin_menu', 'remove_options_from_agency_admin' );

function remove_options_from_agency_admin() {
	// creating functions post_remove for removing menu item
	$current_user = wp_get_current_user();
	$get_role     = $current_user->roles[0];
	if ( $get_role == 'agency_admin' ) {
		remove_menu_page( 'edit.php?post_type=acf-field-group' );
		remove_menu_page( 'options-general.php' );
	}

}

add_action( 'admin_menu', 'remove_options_from_teamusers' );

function remove_options_from_teamusers() {
	// creating functions post_remove for removing menu item
	$current_user = wp_get_current_user();
	$get_role     = $current_user->roles[0];
	if ( $get_role == 'team-author' || $get_role == 'team-lead' ) {
		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit.php?post_type=acf-field-group' );
		remove_menu_page( 'edit.php?post_type=webchat' );
		remove_menu_page( 'acf-options' );
		remove_menu_page( 'options-general.php' );
	}

}

add_action( 'admin_menu', 'remove_options_from_regionalusers' );

function remove_options_from_regionalusers() {
	// creating functions post_remove for removing menu item
	$current_user = wp_get_current_user();
	$get_role     = $current_user->roles[0];
	if ( $get_role == 'regional-editor' ) {
		remove_menu_page( 'edit.php' );
	}
}

add_action( 'admin_menu', 'dw_remove_menu_items' );

function dw_remove_menu_items() {
    if( !current_user_can( 'administrator' ) ):
      // remove_menu_page( 'edit.php' );
      remove_menu_page( 'edit-comments.php' );
      remove_menu_page( 'tools.php' );
      remove_menu_page( 'themes.php' );
    endif;
}

