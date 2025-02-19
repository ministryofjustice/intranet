<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

class AddTeamRolesToAdministrator
{
    protected $capabilities = [
        'edit_others_team_news',
        'edit_team_news',
        'publish_team_news',
        'read_private_team_news',
        'edit_others_team_blogs',
        'edit_team_blogs',
        'publish_team_blogs',
        'read_private_team_blogs',
        'edit_others_team_events',
        'edit_team_events',
        'publish_team_events',
        'read_private_team_events',
        'edit_others_team_profiles',
        'edit_team_profiles',
        'publish_team_profiles',
        'read_private_team_profiles',
        'edit_others_team_pages',
        'edit_team_pages',
        'publish_team_pages',
        'read_private_team_pages',
        'edit_others_team_specialists',
        'edit_team_specialists',
        'publish_team_specialists',
        'read_private_team_specialists'
    ];

    public function __construct()
    {
        $administrator_role = get_role('administrator');

        foreach ($this->capabilities as $cap) {
            if (empty($administrator_role->capabilities[$cap])) {
                $administrator_role->add_cap($cap);
            }
        }
    }
}
