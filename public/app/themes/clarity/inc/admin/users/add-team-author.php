<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

/**
 * Team Author User Role
 * 
 * Changes to this file are applied on app. startup, via `wp sync-user-roles sync`.
 * @see public/app/themes/clarity/inc/commands/SyncUserRoles.php
 *
 * @package Clarity
 */

class TeamAuthorRole extends Role
{
    protected string $name = 'team-author';

    protected string $display_name = 'Team Author';

    protected array $capabilities = [
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
    ];
}
