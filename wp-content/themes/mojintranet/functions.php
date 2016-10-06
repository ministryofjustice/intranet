<?php
// Includes and requires
include     ('helpers/debug.php');                        // Debug tool
include     ('helpers/cachebuster.php');                  // Ensures updated CSS and JS are served to client
include     ('helpers/taggr.php');                        // Tool for retrieving pages by their dw-tag
include     ('helpers/validation.php');                   // Server side validation

include     ('inc/utilities/agency-editor.php');          // Agency Editor utility
include     ('inc/utilities/agency-context.php');         // Agency Context utility

require_once('inc/authors.php');                          // Additional author information
require_once('inc/cache.php');                            // Amend the cache headers
require_once('inc/cookies.php');                            // Amend the cache headers
require_once('inc/comments.php');                         // Add extra commenting functionality
require_once('inc/customiser-controls.php');              // Extra customiser controls
require_once('inc/dependencies.php');                     // CSS/JS dependency enqueing
require_once('inc/documents.php');                        // Control how documents are handled
require_once('inc/excerpts.php');                         // Excerpt filters
require_once('inc/images.php');                           // Images sizes and functions
require_once('inc/languages.php');                        // Controls the site language(s)
require_once('inc/mail.php');                        	  // Mail Functions
require_once('inc/user-management.php');                  // Additional author information
require_once('inc/news-customiser.php');                  // Setup news customiser
require_once('inc/post-types.php');                       // Controls post-types (custom and built-in)
require_once('inc/post-fork.php');                   	  // Add option to fork posts and pages
require_once('inc/taxonomies.php');                       // Custom taxonomies
require_once('inc/list-tables.php');                      // Adjustments to list tables
require_once('inc/admin-commands.php');                   // Admin commands
require_once('inc/menu-locations.php');                   // Register menu locations
require_once('inc/option-pages.php');                     // Option Pages
require_once('inc/page-options.php');                     // Page Options
require_once('inc/query-vars.php');                       // Register custom query variables
require_once('inc/redirects.php');                        // Site redirects
require_once('inc/searching.php');                        // Functions to enhance searching (using Relevanssi)
require_once('inc/security.php');                         // Security functions
require_once('inc/sidebars.php');                         // Register sidebars
require_once('inc/tidy-up.php');                          // Tidy up CMS
require_once('inc/titles.php');                           // Title filters
require_once('inc/uploads.php');                          // File uploads
require_once('inc/user-roles.php');                       // User roles
require_once('inc/utility.php');                          // Utility functions
require_once('inc/wp-admin-bar.php');                     // Control the WP Admin Bar

require_once('admin/editor-enhancements.php');            // Adds enhancements to post/page editor screen
require_once('admin/errors.php');                         // Displays errors in admin
require_once('admin/dashboard.php');                      // Dashboard functions
require_once('admin/listing.php');                        // Listing functions

add_action( 'after_setup_theme', 'mojintranet_setup' );

if ( !function_exists( 'mojintranet_setup' ) ) {
	function mojintranet_setup() {

		// This theme uses post thumbnails
		add_theme_support( 'post-thumbnails' );

		// Make theme available for translation
		// Translations can be filed in the /languages/ directory
		load_theme_textdomain( 'mojintranet', TEMPLATEPATH . '/languages' );

		$locale = get_locale();
		$locale_file = TEMPLATEPATH . "/languages/$locale.php";
		if ( is_readable( $locale_file ) )
			require_once( $locale_file );
	}
}
