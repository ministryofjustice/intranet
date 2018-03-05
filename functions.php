<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    die();
}

// WP admin area related functions and methods
require_once('inc/admin/comments.php');               // Editor/admin comment management. Turn comments on/off etc
require_once('inc/admin/wp-admin-bar.php');           // Relates to WP admin bar at the top of the intranet when logged in.
require_once('inc/admin/suppress-wp-update-msg.php'); // Disables WP notice to editors to update version.

// WP site functions and methods

require_once('inc/acf.php');                    // Advanced Custom Fields plugin related functions
require_once('inc/autoloader.php');             // Custom theme autoloader (should replace with PSR4!).
require_once('inc/cookies.php');                // Where all cookies are managed (Child theme specific)
require_once('inc/constants.php');              // Site wide constants being set.
require_once('inc/enqueue.php');                // Scripts and stylesheets for child theme are loading into Wordpress.
require_once('inc/form-builder.php');           // Custom function for working with forms. Probably should be refactored into forms.php
require_once('inc/forms.php');                  // Form related methods.
require_once('inc/get-component.php');          // Function being used to call in various components
require_once('inc/get-intranet-code.php');      // Being used as a filter to filter out specific agencies.
require_once('inc/menu.php');                   // Additional functionality relating to theme menus
require_once('inc/search.php');                 // All search related functions
require_once('inc/utilities.php');              // Utility functions
require_once('inc/get-posts-rest-api.php');     // Pulls posts through the REST API
require_once('inc/get-campaign-posts-api.php'); // Pulls campaign posts through the REST API
require_once('inc/get-news-rest-api.php');      // Pulls news through the REST API
require_once('inc/get-campaign-news-api.php');  // Pulls campaign news through the REST API
require_once('inc/get-region-news-api.php');    // Pulls region news through the REST API
require_once('inc/get-events-api.php');         // Pulls events api
require_once('inc/pagination.php');             // Pagination function
require_once('inc/comments.php');
require_once('inc/add-eventsdates-rest-api.php');
