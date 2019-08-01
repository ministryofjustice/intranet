<?php
/**
 * Agency Editor User Role
 *
 * Agency Editor:
 * Third greatest permission group.
 *
 * @package Clarity
 */

$capabilities = array(
	// management
	'opt_in_content'             => true,
	'homepage_all_access'        => true,
	'moderate_comments'          => true,
	'manage_links'               => true,
	'manage_terms'               => true,
	'manage_categories'          => true,
	'assign_news_categories'     => true,
	'assign_campaign_categories' => true,
	'assign_resource_categories' => true,
	'unfiltered_html'            => true,
	'assign_agencies_to_posts'   => true,

	'upload_files'               => true,
	'read'                       => true,

	// posts
	'create_posts'               => true,
	'edit_posts'                 => true,
	'edit_others_posts'          => true,
	'edit_private_posts'         => true,
	'edit_published_posts'       => true,
	'publish_posts'              => true,
	'delete_posts'               => true,
	'delete_published_posts'     => true,
	'delete_others_posts'        => true,
	'delete_private_posts'       => true,
	'read_private_posts'         => true,

	// pages
	'edit_pages'                 => true,
	'edit_others_pages'          => true,
	'edit_published_pages'       => true,
	'edit_private_pages'         => true,
	'publish_pages'              => true,
	'delete_pages'               => true,
	'delete_others_pages'        => true,
	'delete_published_pages'     => true,
	'delete_private_pages'       => true,
	'read_private_pages'         => true,

	// documents
	'edit_documents'             => true,
	'edit_others_documents'      => true,
	'edit_private_documents'     => true,
	'edit_published_documents'   => true,
	'delete_documents'           => true,
	'delete_others_documents'    => true,
	'delete_posts'               => true,
	'delete_private_documents'   => true,
	'delete_private_posts'       => true,
	'delete_published_documents' => true,
	'delete_published_posts'     => true,
	'read_private_documents'     => true,
	'publish_documents'          => true,

	// Events
	'edit_events'                => true,
	'edit_event'                 => true,
	'edit_others_events'         => true,
	'edit_published_events'      => true,
	'publish_events'             => true,
	'delete_event'               => true,
	'delete_others_events'       => true,

	// news
	'edit_news'                  => true,
	'edit_others_news'           => true,
	'publish_news'               => true,
	'read_private_news'          => true,
	'delete_news'                => true,
	'delete_others_news'         => true,
	'delete_published_news'      => true,
	'delete_private_news'        => true,

);

if ( get_role( 'agency-editor' ) ) {
	remove_role( 'agency-editor' );
}

// check if role doesnt exist
$wp_roles = new WP_Roles();
if ( ! role_exists( 'agency-editor' ) ) {
	add_role( 'agency-editor', 'Agency Editor', $capabilities );
}
