<?php

add_action('wp_dashboard_setup', 'dw_remove_dashboard_widgets');

// Hide dashboard widgets
function dw_remove_dashboard_widgets()
{
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    // remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
    remove_meta_box('recently-edited-content', 'dashboard', 'normal');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_action('welcome_panel', 'wp_welcome_panel');
}

/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
if (! current_user_can('subscriber')) :
    add_action('wp_dashboard_setup', 'help_editors_add_dashboard_widgets');

    function help_editors_add_dashboard_widgets()
    {
        add_meta_box(
            'help_editors_dashboard_widget',   // Widget slug.
            'Editing on the intranet',                // Title.
            'help_editors_dashboard_widget_function',  // Display function.
            'dashboard',
            'side'
        );
    }
endif;

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function help_editors_dashboard_widget_function()
{
     // Display whatever it is you want to show.
    echo '
  Guidance for editing the MoJ intranet wiki is at <br><a href="https://intranet.justice.gov.uk/guidance/it-services/editing-the-intranet">https://intranet.justice.gov.uk/guidance/it-services/editing-the-intranet</a><br><br>To catch up with the latest developments and for further discussion around editing on the MoJ intranet, join the editor community Slack channel at <a href="https://mojdt.slack.com/messages/C9XUZU0J3/team/U57TUHZ5W/">#intranet-editors.</a> <br><br> Technical issues with this platform can be emailed to <br><a href="mailto:intranet-support@digital.justice.gov.uk">intranet-support@digital.justice.gov.uk</a>
  ';
}
