<?php

namespace MOJ\Intranet;

use Agency_Context;

defined('ABSPATH') || exit;

/**
 * Archived Content is not a post type in itself,
 * but a collection of posts that have been archived.
 * It is used to group content that has been removed from the main site,
 * but is still accessible for reference.
 */
class ArchivedContent
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'addMenuPage']);
    }

    public function addMenuPage()
    {
        add_menu_page(
            __('Archived Content', 'clarity'),
            'Archived Content',
            'archived_content', // This capability is used to restrict access to the archived content menu
            'archived-content',
            [$this, 'renderMenuPage'],
            'dashicons-welcome-write-blog'
        );
    }

    /**
     * Get the submenu items for the archived content menu.
     * 
     * This method retrieves the submenu items that are registered under the 'archived-content' menu.
     * It filters the items to only include those that the current user has permission to access.
     * 
     * @return array An array of submenu items, each item is an array containing the title, capability, and URL.
     */
    static function getSubmenuItems()
    {
        global $submenu;

        // Get the submenu items for the archived content menu
        $submenu_list = $submenu['archived-content'] ?? [];

        // Remove the first item (the main menu item)
        if (!empty($submenu_list) && isset($submenu_list[0])) {
            array_shift($submenu_list);
        }

        // Filter the submenu items to only include those the user can access.
        return array_filter($submenu_list, function ($item) {
            $capability = $item[1];
            return current_user_can($capability);
        });
    }

    /**
     * Render the archived content menu page.
     * 
     * This method displays the content of the archived content menu page,
     * including a list of submenu items that are available to the user.
     * 
     * @return void
     */
    static function renderMenuPage(): void
    {
        $submenu_items = self::getSubmenuItems(); ?>

        <h1>Archived Content</h1>

        <?php if (empty($submenu_items)): ?>
            <p>No archived content available.</p>
        <?php else: ?>
            <p>The following content is archived for future reference, and read-only.</p>
            <ul>
                <?php foreach ($submenu_items as $item): ?>
                    <li><a href="<?= esc_url(admin_url($item[2])); ?>"><?= esc_html($item[0]); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php
    }
}

new ArchivedContent();
