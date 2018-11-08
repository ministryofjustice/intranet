<?php
/**
 * Regional Editor User Role
 *
 * Regional Editor:
 * Third greatest permission group.
 *
 * @package Clarity
 */

$capabilities = array(
	'moderate_comments'             => true,
	'manage_links'                  => true,
	'unfiltered_html'               => true,
	'upload_files'                  => true,

	'read'                          => true,

	'edit_posts'                    => true,
	'edit_others_posts'             => true,
	'delete_posts'                  => true,
	'delete_private_posts'          => true,
	'delete_published_posts'        => true,
	'publish_posts'                 => true,

	'edit_documents'                => true,
	'edit_others_documents'         => true,
	'edit_private_documents'        => true,
	'edit_published_documents'      => true,
	'delete_documents'              => true,
	'delete_others_documents'       => true,
	'delete_posts'                  => true,
	'delete_private_documents'      => true,
	'delete_private_posts'          => true,
	'delete_published_documents'    => true,
	'delete_published_posts'        => true,
	'read_private_documents'        => true,
	'publish_documents'             => true,

	'edit_events'                   => true,
	'edit_others_events'            => true,
	'publish_events'                => true,

	'edit_others_regional_news'     => true,
	'edit_others_regional_pages'    => true,
	'edit_published_regional_pages' => true,
	'edit_regional_news'            => true,
	'edit_regional_pages'           => true,
	'edit_regional_page'            => true,
	'publish_regional_news'         => true,
	'publish_regional_pages'        => true,
	'read_private_documents'        => true,
	'read_private_regional_news'    => true,
	'read_private_regional_pages'   => true,
	'read_regional_page'            => true,
	'delete_others_regional_pages'  => true,
	'delete_regional_news'          => true,
	'delete_regional_page'          => true,
);

if ( get_role( 'regional-editor' ) ) {
	remove_role( 'regional-editor' );
}

// check if role doesnt exist
$wp_roles = new WP_Roles();
if ( ! role_exists( 'regional-editor' ) ) {
	add_role( 'regional-editor', 'Regional Editor', $capabilities );
}
