<?php
/**
 * Team Lead User Role
 * 
 * If you edit this file, you must sync the roles to the database by running the 
 * SyncUserRoles admin action. 
 * Navigate to Tools > Admin Commands > Sync user roles from codebase to database
 *
 * @package Clarity
 */

$capabilities = array(
    'upload_files'                  => true,
    'edit_posts'                    => true,
    'read'                          => true,
    'delete_team_blogs'             => true,
    'delete_team_events'            => true,
    'delete_team_news'              => true,
    'delete_team_pages'             => true,
    'delete_team_specialists'       => true,
    'edit_others_team_blogs'        => true,
    'edit_others_team_events'       => true,
    'edit_others_team_news'         => true,
    'edit_others_team_pages'        => true,
    'edit_others_team_specialists'  => true,
    'edit_team_blogs'               => true,
    'edit_team_events'              => true,
    'edit_team_news'                => true,
    'edit_team_pages'               => true,
    'edit_team_specialists'         => true,
    'publish_team_blogs'            => true,
    'publish_team_events'           => true,
    'publish_team_news'             => true,
    'publish_team_pages'            => true,
    'publish_team_specialists'      => true,
    'read_private_team_blogs'       => true,
    'read_private_team_events'      => true,
    'read_private_team_news'        => true,
    'read_private_team_pages'       => true,
    'read_private_team_specialists' => true,

    // These are only here so that this role can view wp admin bar features
    'edit_regional_news'            => true,
    'edit_regional_pages'           => true,
);

if (get_role('team-lead')) {
    remove_role('team-lead');
}

// check if role doesnt exist
$wp_roles = new WP_Roles();
if (! role_exists('team-lead')) {
    add_role('team-lead', 'Team Lead', $capabilities);
}
