<?php

/**
 * This section modifies the TotalPoll plugin in admin to suit our theme
 * For frontend polls code see src/components/c-polls
 */

// Check if plugin is activated
if ( in_array( 'totalpoll-lite/plugin.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :

	/**
	 * Remove (for editors) options and settings not needed.
	 */
	add_action( 'admin_notices', 'remove_poll_meta_boxes_and_tabs' );

	// Remove everything that an editor doesn't need to see.
	function remove_poll_meta_boxes_and_tabs() {

		// Get the current admin screen and use it as a check so we only add the CSS to the poll plugin dashboard
		$currentAdminScreen = get_current_screen();

		if ( ! current_user_can( 'administrator' ) && ( $currentAdminScreen->id === 'poll_page_dashboard' ) ) {
		 	echo '<style> .totalpoll-page-tabs { display:none !important }</style>';
			echo '<style> .totalpoll-column-sidebar { display:none !important }</style>';
			echo '<style> .totalpoll-box-totalsuite { display:none !important }</style>';
			echo '<style> .totalpoll-pro-badge { display:none !important }</style>';
			echo '<style> .totalpoll-pro-badge-container { display:none !important }</style>';
			echo '<style> .totalpoll-overview-item-segment:nth-of-type(2) { display:none !important }</style>';		
		}
	}

	// Remove lefthand menu items
	add_action( 'admin_menu', 'remove_menu_links', 9999 );

	function remove_menu_links() {
		if ( ! current_user_can( 'administrator' ) ) {
			remove_submenu_page( 'edit.php?post_type=poll', 'upgrade-to-pro' );
			remove_submenu_page( 'edit.php?post_type=poll', 'options' );
			remove_submenu_page( 'edit.php?post_type=poll', 'extensions' );
			remove_submenu_page( 'edit.php?post_type=poll', 'templates' );
			remove_submenu_page( 'edit.php?post_type=poll', 'log' );
			remove_submenu_page( 'edit.php?post_type=poll', 'insights' );
			remove_submenu_page( 'edit.php?post_type=poll', 'entries' );
		}
	}

else :

	trigger_error( "Hey! TotalPolls plugin has been deactivated and I'm using it (inc/polls.php).", E_USER_NOTICE );

endif;
