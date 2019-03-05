<?php
use MOJ\Intranet;

require_once ABSPATH . 'wp-admin/includes/screen.php';

/**
 * Pick up ACF fields from parent theme
 */
add_filter( 'acf/settings/load_json', 'my_acf_json_load_point' );

function my_acf_json_load_point( $paths ) {
	// append path
	$paths[] = get_template_directory() . '/acf-json';

	// return
	return $paths;
}

/***
*
* Create homepage section in lefthand menu - ACF options
* https://www.advancedcustomfields.com/resources/acf_add_options_page/
*/
add_action( 'init', 'homesettings_option_pages' );

function homesettings_option_pages() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => 'Homepage',
				'capability' => 'homepage_all_access',
				'icon_url'   => 'dashicons-admin-home',
				'menu_title' => 'Homepage',
				'menu_slug'  => 'homepage-settings',
				'position' 	=> '2',
				'redirect'   => false,
			)
		);
	}
}

add_filter( 'acf/load_field', 'home_option_fields' );

function home_option_fields( $field ) {
	$screen = get_current_screen();

	if ( isset( $screen ) && ( $screen->id == 'toplevel_page_homepage-settings' ) ) {
		$context       = Agency_Context::get_agency_context();
		$field['name'] = $context . '_' . $field['name'];
	}
	return $field;
}


/***
*
* Create header section in lefthand menu - ACF options
* https://www.advancedcustomfields.com/resources/acf_add_options_page/
*/
add_action( 'init', 'headersettings_option_pages' );

function headersettings_option_pages() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => 'Header settings',
				'capability' => 'homepage_all_access',
				'icon_url'   => 'dashicons-editor-table',
				'menu_title' => 'Header',
				'menu_slug'  => 'header-settings',
				'position' 	=> '1',
				'redirect'   => false,
			)
		);
	}
}

add_filter( 'acf/load_field', 'header_option_fields' );

function header_option_fields( $field ) {
	$screen = get_current_screen();

	if ( isset( $screen ) && ( $screen->id == 'toplevel_page_header-settings' ) ) {
		$context       = Agency_Context::get_agency_context();
		$field['name'] = $context . '_' . $field['name'];
	}
	return $field;
}

// ‘Admin Only’ - ‘Yes/No’ setting to all fields allowing them to be hidden from non admin users - https://www.advancedcustomfields.com/resources/adding-custom-settings-fields/
function my_admin_only_render_field_settings( $field ) {
	acf_render_field_setting(
		$field, array(
			'label'        => __( 'Admin Only?' ),
			'instructions' => '',
			'name'         => 'admin_only',
			'type'         => 'true_false',
			'ui'           => 1,
		), true
	);
}
add_action( 'acf/render_field_settings', 'my_admin_only_render_field_settings' );

// This will allow us to check if the current user is an administrator, and if not, prevent the field from being displayed.
function my_admin_only_load_field( $field ) {

	// bail early if no 'admin_only' setting
	if ( empty( $field['admin_only'] ) ) {
		return $field;
	}
	// return false if is not admin (removes field)
	if ( ! current_user_can( 'administrator' ) ) {
		return false;
	}

	return $field;
}
add_filter( 'acf/load_field', 'my_admin_only_load_field' );


// hide drafts
function relationship_options_filter( $args, $field, $the_post ) {
	$args['post_status'] = array( 'publish' );

	return $args;
}
add_filter( 'acf/fields/post_object/query', 'relationship_options_filter', 10, 3 );
