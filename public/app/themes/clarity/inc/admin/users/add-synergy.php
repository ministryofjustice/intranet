<?php
/**
 * Synergy User Role
 * 
 * Changes to this file are applied on app. startup, via `wp sync-user-roles sync`.
 * @see public/app/themes/clarity/inc/commands/SyncUserRoles.php
 *
 * @package Clarity
 */

$capabilities = array(
    'synergy' => true
);

if (get_role('synergy')) {
    remove_role('synergy');
}

// Check if role doesn't exist
$wp_roles = new WP_Roles();
if (! role_exists('synergy')) {
    add_role('synergy', 'Synergy Bot', $capabilities);
}
