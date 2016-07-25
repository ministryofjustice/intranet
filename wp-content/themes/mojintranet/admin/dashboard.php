<?php

// Hide dashboard widgets
function dw_remove_dashboard_widgets() {
  remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
  remove_meta_box( 'recently-edited-content', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
  remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
  remove_action( 'welcome_panel', 'wp_welcome_panel' );
}
add_action('wp_dashboard_setup', 'dw_remove_dashboard_widgets' );
