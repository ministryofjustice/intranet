<?php

/**
 * Parses <p> tags from content - WP adds <p> tag to the_content which results in
 */
add_filter( 'the_content', 'fix_shortcode_p_gaps' );

function fix_shortcode_p_gaps( $content = null ) {
	$block = join( '|', array( 'mostpopular', 'col', 'text-banner' ) );
	$rep   = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", '[$2$3]', $content );
	$rep   = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", '[/$2]', $rep );
	return $rep;
}

/**
 * Display most popular link list
 */
add_shortcode( 'mostpopular', 'hr_most_popular_shortcode' );

function hr_most_popular_shortcode() {
	ob_start();
	get_template_part( 'src/components/c-most-popular/view' );
	return ob_get_clean();
}

/**
 * Allow editors to create two columns in pages
 */
add_shortcode( 'columns', 'clarity_columns_shortcode' );

function clarity_columns_shortcode( $atts, $content = '' ) {
	return '<div class="l-column-wrapper">' . apply_filters( 'the_content', $content ) . '</div>';
}

add_shortcode( 'col', 'clarity_col_shortcode' );

function clarity_col_shortcode( $atts, $content = '' ) {
	return '<div class="l-column-half-section">' . apply_filters( 'the_content', $content ) . '</div>';
}

/**
 * Inline text banner for post and page body content
 */
add_shortcode( 'text-banner', 'clarity_shortcode_banner' );

function clarity_shortcode_banner( $atts ) {

		ob_start();
		include locate_template( 'src/components/c-shortcode-banner/view.php' );
		return ob_get_clean();
}

