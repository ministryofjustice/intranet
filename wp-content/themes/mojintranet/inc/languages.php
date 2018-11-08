<?php

function dw_change_language( $locale ) {
	return 'en-GB';
}
add_filter( 'locale', 'dw_change_language' );

?>
