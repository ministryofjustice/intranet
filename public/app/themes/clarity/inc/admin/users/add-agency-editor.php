<?php
/**
 * Agency Editor User Role
 *
 * Agency Editor:
 * Third greatest permission group.
 * 
 * Changes to this file are applied on app. startup, via `wp sync-user-roles sync`.
 * @see public/app/themes/clarity/inc/commands/SyncUserRoles.php
 *
 * @package Clarity
 */

$capabilities = array(
    // management
    'opt_in_content' => true,
    'homepage_all_access' => true,
    'moderate_comments' => true,
    'manage_links' => true,
    'manage_terms' => true,
    'manage_categories' => true,
    'assign_news_categories' => true,
    'assign_campaign_categories' => true,
    'assign_resource_categories' => true,
    'unfiltered_html' => true,
    'assign_agencies_to_posts' => false,
    'upload_files' => true,
    'read' => true,

    // posts
    'create_posts' => true,
    'edit_posts' => true,
    'edit_others_posts' => true,
    'edit_private_posts' => true,
    'edit_published_posts' => true,
    'publish_posts' => true,
    'delete_posts' => true,
    'delete_published_posts' => true,
    'delete_others_posts' => true,
    'delete_private_posts' => true,
    'read_private_posts' => true,

    // pages
    'edit_pages' => true,
    'edit_others_pages' => true,
    'edit_published_pages' => true,
    'edit_private_pages' => true,
    'publish_pages' => true,
    'delete_pages' => true,
    'delete_others_pages' => true,
    'delete_published_pages' => true,
    'delete_private_pages' => true,
    'read_private_pages' => true,

    // documents
    'edit_documents' => true,
    'edit_others_documents' => true,
    'edit_private_documents' => true,
    'edit_published_documents' => true,
    'delete_documents' => true,
    'delete_others_documents' => true,
    'delete_private_documents' => true,
    'delete_published_documents' => true,
    'read_private_documents' => true,
    'publish_documents' => true,

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

    // Events
    'edit_events' => true,
    'edit_event' => true,
    'edit_others_events' => true,
    'edit_published_events' => true,
    'publish_events' => true,
    'delete_event' => true,
    'delete_others_events' => true,

    // news
    'edit_news' => true,
    'edit_others_news' => true,
    'publish_news' => true,
    'read_private_news' => true,
    'delete_news' => true,
    'delete_others_news' => true,
    'delete_published_news' => true,
    'delete_private_news' => true,

);

if (get_role('agency-editor')) {
    remove_role('agency-editor');
}

// Does the role exist?
if (!role_exists('agency-editor')) {
    add_role('agency-editor', 'Agency Editor', $capabilities);
}
