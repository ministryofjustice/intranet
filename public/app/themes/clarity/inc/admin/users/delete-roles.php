<?php

/**
 * This file is only for removing default/custom user groups
 * 
 * If you edit this file, you must sync the roles to the database by running the 
 * SyncUserRoles admin action. 
 * Navigate to Tools > Admin Commands > Sync user roles from codebase to database
 */

$wp_roles = new WP_Roles();

$wp_roles->remove_role('agency_admin_editor');
$wp_roles->remove_role('author');
$wp_roles->remove_role('contributor');
$wp_roles->remove_role('editor');
$wp_roles->remove_role('section_editor');
