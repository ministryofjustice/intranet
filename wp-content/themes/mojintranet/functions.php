<?php
include('inc/utilities/agency-editor.php');          // Agency Editor utility
include('inc/utilities/agency-context.php');         // Agency Context utility
include('inc/utilities/region-context.php');         // Region Context utility

require_once('inc/ajax.php');                             // Ajax Functions
require_once('inc/languages.php');                        // Controls the site language(s)
require_once('inc/mail.php');                        	  // Mail Functions
require_once('inc/post-fork.php');                   	  // Add option to fork posts and pages
require_once('inc/taxonomies.php');                       // Custom taxonomies

require_once('inc/menu-locations.php');                   // Register menu locations
require_once('inc/option-pages.php');                     // Option Pages
require_once('inc/event-details.php');                    // Event Detail Fields
require_once('inc/page-options.php');                     // Page Options

require_once('inc/searching.php');                        // Functions to enhance searching (using Relevanssi)
require_once('inc/security.php');                         // Security functions

require_once('inc/uploads.php');                          // File uploads

require_once('admin/errors.php');                         // Displays errors in admin
require_once('admin/listing.php');                        // Listing functions

require_once('inc/api.php');                              // API Support for custom post types & taxonomies

