<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists( 'Mega_Menu_Walker_Edit' ) ) :

/**
 * Modify the Edit Walker to allow us to add our own custom fields.
 */
class Mega_Menu_Walker_Edit extends Walker_Nav_Menu_Edit {

	/**
	 *
     * @since 1.0
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$item_output = '';

		parent::start_el( $item_output, $item, $depth, $args, $id );

		$position = '<p class="field-move';

		$extra = $this->get_fields( $item, $depth, $args, $id );

		$output .= str_replace( $position, $extra . $position, $item_output );
	}

	/**
	 *
     * @since 1.0
	 */
	protected function get_fields( $item, $depth, $args = array(), $id = 0 ) {
		ob_start();

		// conform to https://core.trac.wordpress.org/attachment/ticket/14414/nav_menu_custom_fields.patch
		do_action( 'wp_nav_menu_item_custom_fields', $id, $item, $depth, $args );

		return ob_get_clean();
	}
}

endif;