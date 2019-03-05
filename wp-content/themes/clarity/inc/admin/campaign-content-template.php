<?php

/**
 * WP Admin Bar modifications
 */
add_action( 'acf/render_field', 'dw_add_contrast_message', 10, 1 );

function dw_add_contrast_message( $field ) {

	if ( ! empty( $field['wrapper'] ) && ! empty( $field['wrapper']['class'] ) && strpos( $field['wrapper']['class'], 'colour_check' ) !== false ) {
		echo '<div class="acf-error-message contrast_invalid_message"><p>To choose an accessible colour, go to http://colorsafe.co for help picking an accessible colour.</p></div>';
		echo '<div class="acf-error-message contrast_valid_message"><p>The colour you have entered meets our AA accessibility requirements.</p></div>';
	}
}

add_action( 'wp_ajax_check_colour_contrast', 'dw_check_colour_contrast' );
add_action( 'wp_ajax_nopriv_check_colour_contrast', 'dw_check_colour_contrast' );

function dw_check_colour_contrast() {
	$colour1  = $_GET['colour1'];
	$colour2  = $_GET['colour2'];
	$success  = false;
	$contrast = 0;

	if ( ! empty( $colour1 ) && ! empty( $colour2 ) ) {
		$contrast = dw_colour_diff( dw_hex_to_rgb( $colour1 ), dw_hex_to_rgb( $colour2 ) );
		$success  = true;
	}

	echo json_encode(
		[
			'success'  => $success,
			'contrast' => $contrast,
		]
	);
	die();
}

function dw_hex_to_rgb( $hex ) {
	$hex = str_replace( '#', '', $hex );

	if ( strlen( $hex ) == 3 ) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} else {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	}
	$rgb = array(
		'r' => $r,
		'g' => $g,
		'b' => $b,
	);
	return $rgb; // returns an array with the rgb values
}

function dw_colour_diff( $colour1, $colour2 ) {
	return max( $colour1['r'], $colour2['r'] ) - min( $colour1['r'], $colour2['r'] ) +
	max( $colour1['g'], $colour2['g'] ) - min( $colour1['g'], $colour2['g'] ) +
	max( $colour1['b'], $colour2['b'] ) - min( $colour1['b'], $colour2['b'] );
}

add_filter( 'acf/update_value/key=field_587649cf122f3', 'dw_check_colour_field', 10, 3 );

function dw_check_colour_field( $value, $post_id, $field ) {
	if ( substr( $value, 0, 1 ) != '#' ) {
		$value = '#' . $value;
	}
	return $value;
}

