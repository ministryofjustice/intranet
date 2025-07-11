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
 * Changes to this file are applied on app. startup, via `wp sync-user-roles sync`.
 * @see public/app/themes/clarity/inc/commands/SyncUserRoles.php
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
    'manage_prior_party_banners' => true,

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
    // Retain edit permissions, so that the edit screen is available,
    // as these users are not the author, this results in a read-only view.
    'edit_notes_from_antonia' => true,
    'edit_note_from_antonia' => true,
    'edit_others_notes_from_antonia' => false,
    'edit_published_notes_from_antonia' => false,
    'publish_notes_from_antonia' => false,
    'delete_notes_from_antonia' => false,
    'delete_published_notes_from_antonia' => false,
    'delete_private_notes_from_antonia' => false,
    'delete_note_from_antonia' => false,
    'delete_others_notes_from_antonia' => false,

    // Notes from Amy
    'note_from_amy' => true,
    'notes_from_amy' => true,
    'edit_notes_from_amy' => true,
    'edit_note_from_amy' => true,
    'edit_others_notes_from_amy' => false,
    'edit_published_notes_from_amy' => false,
    'publish_notes_from_amy' => false,
    'delete_notes_from_amy' => false,
    'delete_published_notes_from_amy' => false,
    'delete_private_notes_from_amy' => false,
    'delete_note_from_amy' => false,
    'delete_others_notes_from_amy' => false,

    // Notes from Jo
    'note_from_jo' => true,
    'notes_from_jo' => true,
    'edit_notes_from_jo' => true,
    'edit_note_from_jo' => true,
    'edit_others_notes_from_jo' => true,
    'edit_published_notes_from_jo' => true,
    'publish_notes_from_jo' => true,
    'delete_notes_from_jo' => true,
    'delete_published_notes_from_jo' => true,
    'delete_private_notes_from_jo' => true,
    'delete_note_from_jo' => true,
    'delete_others_notes_from_jo' => true,

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

    // Archived Content
    'archived_content' => true,
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
