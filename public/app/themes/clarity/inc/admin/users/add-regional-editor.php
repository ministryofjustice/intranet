<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

/**
 * Regional Editor User Role
 *
 * Regional Editor:
 * Third greatest permission group.
 * 
 * Changes to this file are applied on app. startup, via `wp sync-user-roles sync`.
 * @see public/app/themes/clarity/inc/commands/SyncUserRoles.php
 *
 * @package Clarity
 */

class RegionalEditorRole extends Role
{

    protected string $name = 'regional-editor';

    protected string $display_name = 'Regional Editor';

    protected array $capabilities = [
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
        'edit_event'                    => true,
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
        'delete_published_pages'        => true,
        'delete_published_regional_pages' => true,
    ];

    /**
     * Hooks
     * 
     * Don't load these in a constructor, because the must be loaded for standard requests,
     * but are not needed for WP CLI commands.
     */
    public function hooks()
    {
        add_filter('map_meta_cap', [$this, 'setDoNotAllow'], 10, 3);
    }

    /**
     * Set Do Not Allow
     *
     * On multisite, by default the super-administrator role can do anything.
     * This is not useful certain cases. To effectively remove a capability from 
     * super-administrator, we need to add a special do_not_allow entry to the
     * meta capabilities array.
     *
     * Specifically, here we are adding do_not_allow unless the user has the
     * regional-editor capability.
     * It stops super-administrators from:
     * - seeing the region context switcher
     * - seeing a filtered list of posts in the post list screens
     *
     * @param array $caps
     * @param string $cap
     * @param int $user_id
     * @return array
     */

    public function setDoNotAllow($caps, $cap, $user_id)
    {
        // Only filter for multisite
        if (!is_multisite()) {
            return $caps;
        }

        /**
         * Only filter checks for custom_capability.
         */
        if ('regional-editor' === $cap) {
            $user      = get_userdata($user_id);
            $user_caps = $user->get_role_caps();

            /**
             * If the user does not have the capability, or it's denied, then
             * add do_not_allow.
             */
            if (empty($user_caps[$cap])) {
                $caps[] = 'do_not_allow';
            }
        }

        return $caps;
    }
}

if (!defined('WP_CLI') || !WP_CLI) {
    (new RegionalEditorRole())->hooks();
}
