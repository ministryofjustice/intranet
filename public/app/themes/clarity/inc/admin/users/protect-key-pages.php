<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

/**
 * Protects key pages from being edited or deleted by non-admin users.
 * 
 * This class checks if a post or page is a key page that should only be editable by administrators.
 * It filters user capabilities to prevent non-admin users from editing or deleting these key pages.
 */
class ProtectKeyPages
{
    // Key templates for pages that only admins can edit.
    const KEY_TEMPLATES = [
        'page_blog.php',
        'page_blogroll.php',
        'page_deleteuser.php',
        'page_events.php',
        'page_guidance_and_support_index.php',
        'page_home.php',
        'page_news.php',
    ];

    // Key slugs for pages that only admins can edit.
    const KEY_SLUGS = [
        'agency-switcher',
    ];

    function __construct()
    {
        add_filter('user_has_cap', [$this, 'filterUserCapabilities'], 10, 3);
    }

    /**
     * Checks is a post or page is limited to editing by admin only.
     * e.g. the page that has been slected as the front page, or a page with the template of page_blog.php
     * 
     * @param int $object_id The ID of the post or page to check.
     * @return bool True if the post is a special post that only admins can edit, false otherwise.
     */
    public function isKeyPage(int $object_id): bool
    {
        $post = get_post($object_id);

        if (!$post || 'page' !== $post->post_type) {
            return false; // Post does not exist, or is not a page
        }

        $post_template = get_page_template_slug($post->ID);

        // Check if the page uses a special template.
        if (in_array($post_template, self::KEY_TEMPLATES, true)) {
            return true;
        }

        // Check if the page has a special slug.
        if (in_array($post->post_name, self::KEY_SLUGS, true)) {
            return true;
        }

        return false;
    }

    /**
     * Dynamically filter user capabilities, to block page edits or deletions.
     *
     * Filter the capabilities base on:
     * - if the user is an administrator
     * - the requested capability
     * - the object ID (typically a post or page ID)
     * 
     * @param bool[]   $allcaps Array of key/value pairs where keys represent a capability name
     *                          and boolean values represent whether the user has that capability.
     * @param string[] $caps    Required primitive capabilities for the requested capability.
     * @param array    $args {
     *     Arguments that accompany the requested capability check.
     *
     *     @type string    $0 Requested capability.
     *     @type int       $1 Concerned user ID.
     *     @type mixed  ...$2 Optional second and further parameters, typically object ID.
     * }
     * @param WP_User  $user    The user object.
     * @return bool[]           Filtered array of capabilities.
     */
    public function filterUserCapabilities($allcaps, $cap, $args)
    {
        // Check if the user is an administrator
        if (isset($allcaps['administrator']) && $allcaps['administrator']) {
            return $allcaps; // Admins have all capabilities
        }

        // Ensure we have a valid capability request.
        if (!isset($args[0]) || !is_string($args[0])) {
            // The first argument is not set or is not a string, so do nothing.
            return $allcaps;
        }

        // The second argument is typically the object ID (post ID, page ID, etc.)
        if (!isset($args[2]) || !is_numeric($args[2])) {
            // The second argument is not set or is numeric, so do nothing.
            return $allcaps;
        }

        // Safe to use $args[2] here - already checked for isset and is_numeric above.
        if (!$this->isKeyPage((int) $args[2])) {
            // If we are not dealing with a key page, do nothing.
            return $allcaps;
        }

        // If we are dealing with a key page, and WP is checking if the user can edit it.
        if ('edit_post' === $args[0]) {
            $allcaps['edit_others_pages'] = false;
        }

        // If we are dealing with a key page, and WP is checking if the user can delete it.
        if ('delete_post' === $args[0]) {
            $allcaps['delete_others_pages'] = false;
            // Removing the manage_options capability is also required to completely drop the ability delete.
            // This can be verified by switching to a non-admin user, visiting the All Pages admin screen,
            // and hovering over a key page - the Bin link should not be visible.
            $allcaps['manage_options'] = false;
        }

        return $allcaps;
    }
}

new ProtectKeyPages();
