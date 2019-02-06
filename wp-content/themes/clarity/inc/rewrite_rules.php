<?php

$path = $_SERVER['REQUEST_URI'];

if ( strpos( $path, 'blog/team-pages/digital-technology' ) ) {
	header( 'Location: ' . home_url() . '/team-pages/data-driven-department-home' );
	exit;
}

if ( strpos( $path, 'blog/team-specialists' ) ) {

	$string      = $path;
	$pattern     = '/(blog\/team-specialists)/i'; // i flag search case-insensitive
	$replacement = 'team-specialists';

	$new_path = preg_replace( $pattern, $replacement, $string );
	header( 'Location: ' . home_url() . $new_path );
}
}
