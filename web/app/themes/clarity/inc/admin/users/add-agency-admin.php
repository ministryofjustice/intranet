<?php

/**
 * Agency Admin permissions
 *
 * Agency Admin:
 * Second greatest permission group.
 * Access to everything within that agency minus a few Administrator settings.
 *
 * Type of user: heads of agencies, non-developer intranet team members
 *
 * @package Clarity
 */


$capabilities = array(

    'news' => true,
    // management
    'edit_dashboard' => true,
    'homepage_all_access' => true,
    'assign_agencies_to_posts' => true,
    'manage_agencies' => true,
    'manage_news_categories' => true,
    'manage_campaign_categories' => true,
    'manage_resource_categories' => true,
    'manage_terms' => true,
    'moderate_comments' => true,
    'manage_categories' => true,
    'manage_links' => true,
    'assign_news_categories' => true,
    'assign_campaign_categories' => true,
    'assign_resource_categories' => true,
    'edit_news_categories' => true,
    'edit_campaign_categories' => true,
    'edit_resource_categories' => true,
    'delete_news_categories' => true,
    'delete_campaign_categories' => true,
    'delete_resource_categories' => true,
    'opt_in_content' => true,
    'unfiltered_html' => true,

    // files
    'export' => true,
    'import' => true,
    'upload_files' => true,

    // users
    'edit_users' => true,
    'create_users' => true,
    'delete_users' => true,
    'list_users' => true,
    'manage_options' => true,
    'promote_users' => true,
    'remove_users' => true,
    'read' => true,

    // posts
    'create_posts' => true,
    'edit_posts' => true,
    'edit_others_posts' => true,
    'edit_published_posts' => true,
    'edit_private_posts' => true,
    'publish_posts' => true,
    'read_private_posts' => true,
    'delete_posts' => true,
    'delete_published_posts' => true,
    'delete_others_posts' => true,
    'delete_private_posts' => true,

    // pages
    'edit_pages' => true,
    'edit_others_pages' => true,
    'edit_published_pages' => true,
    'publish_pages' => true,
    'delete_pages' => true,
    'delete_others_pages' => true,
    'delete_published_pages' => true,
    'delete_private_pages' => true,
    'edit_private_pages' => true,
    'read_private_pages' => true,

    // Notes from Antonia
    'note_from_antonia' => true,
    'notes_from_antonia' => true,
    'edit_notes_from_antonia' => true,
    'edit_note_from_antonia' => true,
    'edit_others_notes_from_antonia' => true,
    'edit_published_notes_from_antonia' => true,
    'publish_notes_from_antonia' => true,
    'delete_notes_from_antonia' => true,
    'delete_published_notes_from_antonia' => true,
    'delete_private_notes_from_antonia' => true,
    'delete_note_from_antonia' => true,
    'delete_others_notes_from_antonia' => true,

    // Documents
    'edit_documents' => true,
    'edit_others_documents' => true,
    'edit_private_documents' => true,
    'edit_published_documents' => true,
    'read_documents' => true,
    'read_document_revisions' => true,
    'read_private_documents' => true,
    'delete_documents' => true,
    'delete_others_documents' => true,
    'delete_private_documents' => true,
    'delete_published_documents' => true,
    'publish_documents' => true,
    'override_document_lock' => true,

    // Events
    'edit_events' => true,
    'edit_event' => true,
    'edit_others_events' => true,
    'edit_published_events' => true,
    'publish_events' => true,
    'delete_event' => true,
    'delete_others_events' => true,

    // news
    'publish_news' => true,
    'read_private_news' => true,
    'edit_news' => true,
    'edit_others_news' => true,
    'delete_news' => true,
    'delete_others_news' => true,
    'delete_published_news' => true,
    'delete_private_news' => true,

    // teams
    'edit_team_blogs' => true,
    'edit_team_news' => true,
    'edit_team_pages' => true,
    'edit_team_specialists' => true,
    'edit_team_events' => true,
    'edit_others_team_blogs' => true,
    'edit_others_team_news' => true,
    'edit_others_team_pages' => true,
    'edit_others_team_specialists' => true,
    'edit_others_team_events' => true,
    'delete_team_blogs' => true,
    'delete_team_news' => true,
    'delete_team_pages' => true,
    'delete_team_specialists' => true,
    'delete_team_events' => true,
    'read_private_team_blogs' => true,
    'read_private_team_news' => true,
    'read_private_team_pages' => true,
    'read_private_team_specialists' => true,
    'read_private_team_events' => true,
    'publish_team_blogs' => true,
    'publish_team_news' => true,
    'publish_team_pages' => true,
    'publish_team_specialists' => true,
    'publish_team_events' => true,

    // regional
    'edit_regional_page' => true,
    'edit_published_regional_pages' => true,
    'edit_others_regional_news' => true,
    'edit_others_regional_pages' => true,
    'edit_regional_news' => true,
    'edit_regional_pages' => true,
    'delete_regional_news' => true,
    'delete_regional_page' => true,
    'delete_others_regional_pages' => true,
    'read_regional_page' => true,
    'read_private_regional_news' => true,
    'read_private_regional_pages' => true,
    'publish_regional_news' => true,
    'publish_regional_pages' => true,

    // allow greater permissions managing polls plugin
    'edit_polls' => true,
    'edit_poll' => true,
    'create_polls' => true,
    'publish_polls' => true,
);


function role_exists($role): bool
{
    if (!empty($role)) {
        return $GLOBALS['wp_roles']->is_role($role);
    }
    return false;
}

if (get_role('agency_admin')) {
    remove_role('agency_admin');
}


// Does the role exist?
if (!role_exists('agency_admin')) {
    add_role('agency_admin', 'Agency Admin', $capabilities);
}

/**
 * Agency Admins cannot modify User roles set as the higher Administrator role.
 * They can still view Administrators.
 */
class Remove_Agency_Admin_Administrator_Access
{
    // Add our filters
    function __construct()
    {
        add_filter('editable_roles', array($this, 'editable_roles'));
        add_filter('map_meta_cap', array($this, 'map_meta_cap'), 10, 4);
    }

    // Remove 'Administrator' from the list of roles if the current user is not an admin
    function editable_roles($roles)
    {
        if (isset($roles['administrator']) && !current_user_can('administrator')) {
            unset($roles['administrator']);
        }

        return $roles;
    }

    // If someone is trying to edit or delete an admin and that user isn't an admin, don't allow it
    function map_meta_cap($caps, $cap, $user_id, $args)
    {
        switch ($cap) {
            case 'edit_user':
            case 'remove_user':
            case 'promote_user':
                if (isset($args[0]) && $args[0] == $user_id) {
                    break;
                } elseif (!isset($args[0])) {
                    $caps[] = 'do_not_allow';
                }
                $other = new WP_User(absint($args[0]));
                if ($other->has_cap('administrator')) {
                    if (!current_user_can('administrator')) {
                        $caps[] = 'do_not_allow';
                    }
                }
                break;
            case 'delete_user':
            case 'delete_users':
                if (!isset($args[0])) {
                    break;
                }
                $other = new WP_User(absint($args[0]));
                if ($other->has_cap('administrator')) {
                    if (!current_user_can('administrator')) {
                        $caps[] = 'do_not_allow';
                    }
                }
                break;
            default:
                break;
        }
        return $caps;
    }
}

$Remove_Agency_Admin_Administrator_Access = new Remove_Agency_Admin_Administrator_Access();
