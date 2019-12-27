<?php
// This file is only for removing default/custom user groups
$wp_roles = new WP_Roles();

$wp_roles->remove_role( 'agency_admin_editor' );
$wp_roles->remove_role( 'author' );
$wp_roles->remove_role( 'contributor' );
$wp_roles->remove_role( 'editor' );
$wp_roles->remove_role( 'section_editor' );
