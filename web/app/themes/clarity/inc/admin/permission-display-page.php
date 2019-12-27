<?php

/**
 * Account access and permissions dashboard
 * Purpose is so that editors have a central dashboard providing all access and security information
 * related to their permission settings.
 *
 * @package Clarity
 */

/**
 *
 * Create the backend admin menu items for permission dashboard
 */
add_action('admin_menu', 'clarity_add_permissions_dashboard');

function clarity_add_permissions_dashboard()
{

    add_submenu_page('profile.php', 'Permissions', 'Your Account Access and Permissions', 'read', 'permissions-dashboard', 'clarity_permissions_dashboard');
}

/**
 *
 * Display the admin permissions dashboard page
 */
function clarity_permissions_dashboard()
{

    global $wp_roles;
    global $current_user;

    $roles             = $current_user->roles;
    $role              = array_shift($roles);
    $user_login_record = get_user_meta($current_user->ID, 'user_login_record', false);
    $last_logged_in    = $user_login_record ? $user_login_record[ count($user_login_record) - 2 ] : 'No login history recorded.';
    $user_data         = get_userdata(get_current_user_id());

    $context  = Agency_Context::get_agency_context();
    $agencies = Agency_Context::current_user_available_agencies();

    if (! Agency_Context::current_user_can_have_context()) :
        return false;
    endif;

    // Your role is
    echo '<div>';
    echo '<div style=" float:left; padding-right: 50px;  max-width: 500px; ">';
    echo '<h1>Your account access and permissions</h1>';
    echo '<h2>Your role is: </h2>';
    esc_attr_e($role);
    echo '<br>';
    echo '<br>';
    echo '<h2>Agency access: </h2>';

    // Agency access, loop through all agencies
    foreach ($agencies as $agency) {
        echo '<div style=" display: inline; ">' . sanitize_text_field($agency) . ', ' . '</div>';
    }
    echo '<br>';
    echo '<br>';

    // Access record
    echo '<h2>Access record: </h2>';
    echo 'Last logged in: ' . sanitize_text_field($last_logged_in);
    echo '<br>';
    echo '<br>';

    // Your current capibilities
    if (is_object($user_data)) {
        $current_user_caps = $user_data->allcaps;

        echo '<h2>Your current capiblities are: </h2>';

        foreach ($current_user_caps as $cap => $key) {
            echo '<ul style=" margin:0; ">';
            echo '<li>' . sanitize_text_field($cap) . '</li>';
            echo '</ul>';
        }

        echo '</div>';
        echo '<div style=" float:left; max-width: 380px; ">';

        // Only admin and agency admin can view profiles
        if (current_user_can('administrator') || current_user_can('agency_admin')) {
                echo '<h2>Role profiles: </h2>';
                echo '<strong>Administrator: </strong>All access, developer access, including technical theme settings.<br><br>';
                echo '<strong>Agency Admin: </strong>Access to most capabilities. Able to manage users, manage all posts and change agencies. Able to edit the homepage.<br><br>';
                echo '<strong>Agency Editor: </strong>Able to create, edit, delete most content. Unable to manage or add users. Able to edit the homepage.<br><br>';
                echo '<strong>Regional Editor: </strong>Only applies to HMCTS regional section. Editor can only modify or edit regional pages.<br><br>';
                echo '<strong>Team Lead: </strong>Able to only edit and manage team area pages and content.<br><br>';
                echo '<strong>Team Author: </strong>Able to only edit team pages and news.<br><br>';
                echo '<strong>Subscriber: </strong>Staff accounts. Unable to do anything except leave comments.<br><br>';
                echo '<br>';
                echo 'The above roles are custom to the MoJ intranet, however, we use many of the standard WordPress capabilities. For more information about these capabilities and related content, visit <a href="https://codex.wordpress.org/Roles_and_Capabilities">Roles and capabilities</a>.';
                echo '<br>';
        }

        echo '</div>';
        echo '</div>';
    }
}
