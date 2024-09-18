<?php
/**
 * Team Author User Role
 * 
 * Changes to this file are applied on app. startup, via `wp sync-user-roles sync`.
 * @see public/app/themes/clarity/inc/commands/SyncUserRoles.php
 *
 * @package Clarity
 */

$capabilities = array(
    'upload_files'                 => true,
    'edit_posts'                   => true,
    'read'                         => true,
    'edit_others_team_blogs'       => true,
    'edit_others_team_events'      => true,
    'edit_others_team_news'        => true,
    'edit_others_team_pages'       => true,
    'edit_others_team_specialists' => true,
    'edit_team_blogs'              => true,
    'edit_team_events'             => true,
    'edit_team_news'               => true,
    'edit_team_pages'              => true,
    'edit_team_specialists'        => true,
    'publish_team_blogs'           => true,
    'publish_team_events'          => true,
    'publish_team_news'            => true,
    'publish_team_pages'           => true,
    'publish_team_specialists'     => true,

    // These are only here so that this role can view wp admin bar features
    'edit_regional_news'           => true,
    'edit_regional_pages'          => true,
);

if (get_role('team-author')) {
    remove_role('team-author');
}

// check if role doesnt exist
$wp_roles = new WP_Roles();
if (! role_exists('team-author')) {
    add_role('team-author', 'Team Author', $capabilities);
}
