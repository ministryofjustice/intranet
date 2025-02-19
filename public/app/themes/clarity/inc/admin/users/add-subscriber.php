<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

/**
 * Remove capabilities from subscribers. - https://codex.wordpress.org/Function_Reference/remove_cap
 */

class RemoveSubscriberCapabilities
{
    public function __construct()
    {
        add_action('init', [$this, 'removeCapabilities']);
    }

    public function removeCapabilities()
    {
        $subscriber = get_role('subscriber');

        $caps = [
            'delete_documents',
            'delete_others_documents',
            'delete_private_documents',
            'delete_published_documents',
            'edit_documents',
            'edit_others_documents',
            'edit_private_documents',
            'edit_published_documents',
            'publish_documents',
            'read_private_documents',
        ];

        foreach ($caps as $cap) {
            if ($subscriber->capabilities[$cap]) {
                $subscriber->remove_cap($cap);
            }
        }
    }
}
