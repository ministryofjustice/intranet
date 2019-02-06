<?php

/**
 *
 * 06.02.19 Team page structure has been changed.
 *
 * Paths changed:
 * /blog/team-pages/digital-technology/data-driven-department-home/
 * /blog/team-specialists/*
 * to
 * /team-pages/data-driven-department-home/
 * /team-specialists/
 **/

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
