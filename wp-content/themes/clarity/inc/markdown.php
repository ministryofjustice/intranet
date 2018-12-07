<?php
/*
*  Filters to display markdown and shortcode correctly as HTML on page
*/
if ( class_exists( 'Markdown_Parser' ) ) :
	add_filter( 'the_content', 'Markdown' );
	add_filter( 'the_excerpt', 'Markdown' );
	add_filter( 'acf_the_content', 'Markdown' );
else :
	trigger_error( 'PHP Markdown Extra plugin deactivated. If using a new markdown plugin, tab template requires you use this filter and change class line 5.', E_USER_NOTICE );
endif;
