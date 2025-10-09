<?php

use MOJ\ClusterHelper;
use Roots\WPConfig\Config;

// Page post type. Add excerpts to pages
add_action('init', 'add_page_excerpts');

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

class CacheHandler
{
    private $purged_urls = [];

    private string $cache_purge_url;

    /**
     * Constructor
     * Initializes the class and sets up the necessary hooks.
     */
    public function __construct()
    {
        $cache_purge_url = Config::get('NGINX_PURGE_CACHE_URL');

        if (empty($cache_purge_url)) {
            // If the NGINX_PURGE_CACHE_URL is not set, we cannot proceed.
            error_log('NGINX_PURGE_CACHE_URL is not set. Cannot purge cache.');
            return;
        }

        $this->cache_purge_url = $cache_purge_url;

        // Add hooks to clear Nginx cache on post actions.
        $this->addHooks();
    }

    public function addHooks()
    {
        // Clear Nginx cache when various post actions occur.
        add_action('wp_after_insert_post', [$this, 'clearNginxCache'], 10, 1);
        add_action('wp_trash_post', [$this, 'clearNginxCache'], 10, 1);
        add_action('before_delete_post', [$this, 'clearNginxCache'], 90, 1);
        add_action('transition_post_status', [$this, 'handlePostStatusTransition'], 10, 3);
    }


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
    public function handlePostStatusTransition(
        string $new_status,
        string $old_status,
        WP_Post $post
    ): void {
        // If the post is a revision, we don't need to clear the cache.
        if ($post->post_type === 'revision') {
            return;
        }

        // Only handle transitions where the post status is actually changed.
        if ($new_status === $old_status) {
            return;
        }

        // Clear the Nginx cache for the post.
        $this->clearNginxCache($post->ID);
    }


    /**
     * Send a purge cache request to all Nginx servers when a post is saved or updated.
     *
     * @param int $post_id The post ID
     * @return void
     */
    public function clearNginxCache(int $post_id): void
    {
        // Start a timer to track the purge requests.
        $start_time = microtime(true);

        if (wp_is_post_revision($post_id)) {
            return; // Do not clear cache for revisions.
        }

        $post_status = get_post_status($post_id);
        if (in_array($post_status, ['auto-draft', 'draft', 'trash'])) {
            // If the post is an auto-draft, draft or in the trash, we can't clear the cache because we can't determine a URL.
            // But, this is fine  for posts in the trash because this function will have been called before the post was trashed.
            return;
        }

        $paths_to_purge = [];

        // Get the post URL.
        $post_url = get_permalink($post_id);
        $post_path = parse_url($post_url, PHP_URL_PATH);

        if (get_post_type($post_id) === 'document') {
            // If we are dealing with a document, these are served without a trailing slash at the end of the path.
            $paths_to_purge[] = rtrim($post_path, '/');
        } else {
            // Get all parent pages, if a blog was published then unpublished, we need to update the parent pages.
            // Do this based on URL. e.g. if /blog/post-name is the URL, we need to clear `/`, `blog` and `blog/post-name`.
            $paths_to_purge = array_reduce(
                explode('/', trim($post_path, '/')),
                function ($acc, $item) {
                    // Append the current item to the last path in the accumulator.
                    $acc[] = $acc[count($acc) - 1] . $item . '/';
                    return $acc;
                },
                ['/'] // Start with the root path
            );
        }

        $cache_purge_url = Config::get('NGINX_PURGE_CACHE_URL');
        if (empty($cache_purge_url)) {
            // If the NGINX_PURGE_CACHE_URL is not set, we cannot proceed.
            error_log('NGINX_PURGE_CACHE_URL is not set. Cannot purge cache.');
            return;
        }

        $curl_start_time = microtime(true);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10); // Set a short timeout
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Host: ' . parse_url(home_url(), PHP_URL_HOST)]);

        // Loop through each URL to purge.
        foreach ($paths_to_purge as $path) :

            $purge_url = $this->cache_purge_url . $path;

            // Use curl to purge the cache - we don't care about the response, and e can't wait for it.
            curl_setopt($ch, CURLOPT_URL, $purge_url);
            curl_exec($ch);

            $curl_errno = curl_errno($ch);
            if ($curl_errno) {
                // If there was an error with the cURL request, log it.
                error_log(sprintf(
                    'cURL error %d while purging URL %s: %s',
                    $curl_errno,
                    $purge_url,
                    curl_error($ch)
                ));
                continue; // Skip to the next URL if there was an error.
            }

            $this->purged_urls[] = $purge_url; // Add the URL to the purged URLs array

        endforeach;

        curl_close($ch);

        $end_time = microtime(true);
        $function_duration = $end_time - $start_time;
        $curl_duration = $end_time - $curl_start_time;

        // Log the purge request.
        error_log(sprintf(
            'Purged %d URLs in %.2f seconds (cURL: %.2f seconds). Endpoint: %s, Paths: %s',
            count($paths_to_purge),
            $function_duration,
            $curl_duration,
            $this->cache_purge_url,
            implode(', ', $paths_to_purge)
        ));
    }
}

new CacheHandler();
