<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

/**
 * Team Lead User Role
 * 
 * Changes to this file are applied on app. startup, via `wp sync-user-roles sync`.
 * @see public/app/themes/clarity/inc/commands/SyncUserRoles.php
 *
 * @package Clarity
 */

class TeamLeadRole extends Role
{

    protected string $name = 'team-lead';

    protected string $display_name = 'Team Lead';

    protected array $capabilities = [
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
    ];
}
