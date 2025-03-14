<?php

// Page post type. Add excerpts to pages
add_action('init', 'add_page_excerpts');
add_action('wp_after_insert_post', 'clear_cache', 10, 2);

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
 * Send a purge cache request to the Nginx server when a post is saved or updated.
 *
 * @param int $post_id The post ID
 *
 * @return void
 */
function clear_cache(int $post_id): void {
    // Check if the post is a revision or unpublished.
    if (wp_is_post_revision($post_id) || get_post_status($post_id) !== 'publish') {
        return;
    }
    // Get the post URL.
    $url = get_permalink($post_id);
    $path = parse_url($url, PHP_URL_PATH);
    $nginx_cache_path = get_home_url(null, '/purge-cache' . $path);
    // Purge the cache.
    $result = wp_remote_get($nginx_cache_path, ['blocking' => false, 'keep_home_url' => true]);
    if (is_wp_error($result)) {
        error_log('Unable to clear cache for path ' . $nginx_cache_path);
        error_log('Error message: ' . $result->get_error_message());
    }
}

