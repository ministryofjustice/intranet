<?php

/*
  Plugin Name: marcin-MVC
  Description: Adds MVC structure to WordPress code
  Author: Marcin Cichon
  Version: 0.1

  Changelog
  ---------
  0.1 - initial release
 */

	if (!defined('ABSPATH')) {
    	exit; // disable direct access
	}

	if (!class_exists('mmvc')) {
		class mmvc {

			function __construct() {
			    define('MVC_PATH', get_template_directory().'/mvc/');
				define('MVC_VIEWS_DIR', 'views/');
				define('MVC_VIEWS_PATH', MVC_PATH.MVC_VIEWS_DIR);

				include_once(plugin_dir_path( __FILE__ ).'Loader.php');
				include_once(plugin_dir_path( __FILE__ ).'Controller.php');
			}

		}

		new mmvc;
	}


	// Force mmvc plugin to loads before all others
	function mmvc_load_first(){
		$path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
		if ( $plugins = get_option( 'active_plugins' ) ) {
			if ( $key = array_search( $path, $plugins ) ) {
				array_splice( $plugins, $key, 1 );
				array_unshift( $plugins, $path );
				update_option( 'active_plugins', $plugins );
			}
		}
	}
	add_action( 'activated_plugin', 'mmvc_load_first',1 );

?>