<?php

add_filter( 'the_content', 'fix_shortcode_p_gaps' );

function fix_shortcode_p_gaps( $content = null ) {
	$block = join( '|', array( 'mostpopular', 'dw_col' ) );
	$rep   = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", '[$2$3]', $content );
	$rep   = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", '[/$2]', $rep );
	return $rep;
}

add_shortcode( 'mostpopular', 'hr_most_popular_shortcode' );

function hr_most_popular_shortcode() {
	ob_start();
	get_template_part( 'src/components/c-most-popular/view' );
	return ob_get_clean();

}
