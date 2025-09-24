<?php

use MOJ\ClusterHelper;

// Page post type. Add excerpts to pages
add_action('init', 'add_page_excerpts');

// Clear Nginx cache when various post actions occur.
add_action('wp_after_insert_post', 'clear_nginx_cache', 10, 1);
add_action('wp_trash_post', 'clear_nginx_cache', 10, 1);
add_action('before_delete_post', 'clear_nginx_cache', 90, 1);
add_action('transition_post_status', 'handle_post_status_transition', 10, 3);

function add_page_excerpts()
{
    add_post_type_support('page', 'excerpt');
}

// Prevent the Agency Switcher page from being overwritten
add_action('save_post', function ($post_id, $post) {
    if ($post->post_name === 'agency-switcher') {
        update_post_meta($post_id, '_wp_page_template', 'agency-switcher.php');
    }
}, 99, 2);


/**
 * Handle post status transitions to clear the Nginx cache.
 *
 * This function is triggered when a post's status changes, allowing us to
 * clear the cache for that post if necessary.
 *
 * @param string $new_status The new post status.
 * @param string $old_status The old post status.
 * @param WP_Post $post The post object.
 */
function handle_post_status_transition(
    string $new_status,
    string $old_status,
    WP_Post $post
): void {
    // Only handle transitions where the post status is actually changed.
    if ($new_status !== $old_status) {
        return;
    }

    // Clear the Nginx cache for the post.
    clear_nginx_cache($post->ID);
}

/**
 * Send a purge cache request to all Nginx servers when a post is saved or updated.
 *
 * @param int $post_id The post ID
 *
 * @return void
 */
function clear_nginx_cache(int $post_id): void
{
    // Check if the post is a revision or unpublished.
    if (wp_is_post_revision($post_id) || get_post_status($post_id) !== 'publish') {
        return;
    }

    // Get the post URL.
    $post_url = get_permalink($post_id);
    $post_path = parse_url($post_url, PHP_URL_PATH);

    // Get all Nginx hosts from the ClusterHelper.
    $cluster_helper = new ClusterHelper();
    $nginx_hosts = $cluster_helper->getNginxHosts('hosts');

    // Loop through each Nginx host and send a purge request.
    foreach ($nginx_hosts as $host) {
        // Construct the full URL for the purge request.
        $nginx_cache_path = $host . '/purge-cache' . $post_path;

        // Purge the cache.
        $result = wp_remote_get($nginx_cache_path, [
            'blocking' => false,
            'headers' => ['Host' => parse_url(home_url(), PHP_URL_HOST)]
        ]);

        // Check for errors in the response.
        if (is_wp_error($result)) {
            error_log(sprintf('Error purging cache at %s: %s', $nginx_cache_path, $result->get_error_message()));
            continue;
        }

        error_log(sprintf('Cache cleared at %s', $nginx_cache_path));
    }
}
