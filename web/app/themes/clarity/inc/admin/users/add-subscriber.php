<?php
/**
 * Remove capabilities from editors. - https://codex.wordpress.org/Function_Reference/remove_cap
 */
function subscriber_set_capabilities()
{
    // Get the role object.
    $subscriber = get_role('subscriber');

    // A list of capabilities to remove from editors.
    $caps = array(
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
    );

    // Remove the capability.
    foreach ($caps as $cap) {
        $subscriber->remove_cap($cap);
    }
}
add_action('init', 'subscriber_set_capabilities');
