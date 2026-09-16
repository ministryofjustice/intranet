<?php

namespace DeliciousBrains\WP_Offload_Media\Tweaks;

/**
 * Amazon S3 and CloudFront tweaks.
 * 
 * A class for functions related to the Amazon S3 and CloudFront.
 * Functions related to cookie signing and Minio belong in the adjacent files.
 */

class AmazonS3AndCloudFrontTweaks
{

    public function __construct()
    {
        // Increase limits from 50 to 5000. 
        add_filter('as3cf_update_replace_provider_urls_batch_size', fn() => 5000);
        add_filter('as3cf_update_filter_post_excerpt_batch_size', fn() => 5000);

        // Increase limits from 500 to 3500, duration is about 45 seconds.
        add_filter('as3cf_update_fix_broken_item_extra_data_batch_size', fn() => 3500);

        // Increase limit from 50 to 750.
        add_filter('as3cf_update_as3cf_items_table_batch_size', fn() => 750);
        // 750 items take ~20 secs, so decrease interval from 2 to 1 minute.
        add_filter('as3cf_update_as3cf_items_table_interval', fn() => 1);

        // Speed up the 3.4 files table migration, from 500 items per 20 secs every 2 minutes.
        // Up to 30 secs every minute, below WordPress's 60 sec cron lock and nginx's 60 sec fastcgi timeout.
        add_filter('as3cf_update_as3cf_files_table_batch_size', fn() => 2000);
        add_filter('as3cf_update_as3cf_files_table_interval', fn() => 1);
        add_filter('as3cf_update_as3cf_files_table_time_limit', fn() => 30);

        // Don't persist the plugin's object cache while its upgrades are running.
        add_action('as3cf_init', [$this, 'maybeDisablePersistentCache']);

        // Redirect legacy URLs to cdn URLs.
        add_action('template_redirect', [$this, 'maybeRedirect404s']);
    }

    /**
     * Keep WP Offload Media's object cache in memory only, while its upgrade routines are running.
     *
     * The plugin caches every item and file that it saves. The 3.4 files table migration saves every item,
     * so these cache entries (~90KB per item, for an hour) fill Redis faster than they expire.
     * On dev, this pushed ElastiCache into swap and caused connection timeouts.
     *
     * @param \Amazon_S3_And_CloudFront $as3cf
     *
     * @return void
     */

    public function maybeDisablePersistentCache($as3cf): void
    {
        // `is_upgraded` was added in 3.4.0, so check it exists in case of a rollback.
        if (!is_object($as3cf) || !method_exists($as3cf, 'is_upgraded') || $as3cf->is_upgraded()) {
            return;
        }

        wp_cache_add_non_persistent_groups(apply_filters('as3cf_object_cache_group', $as3cf->get_plugin_prefix()));
    }

    /**
     * Redirect local media URLs to cdn URLs.
     * 
     * Some content has links to documents and media that have the path `/wp-content/uploads/`.
     * These paths are redirected to `/app/uploads/` by Bedrock.
     * 
     * A further redirect is required to redirect `/app/uploads/` to the CDN URL.
     * 
     * @return void
     */

    public function maybeRedirect404s(): void
    {
        if (!is_404()) {
            return;
        }

        // Check if the request is for a local upload URL.
        if (!str_starts_with($_SERVER['REQUEST_URI'], '/app/uploads/')) {
            return;
        }

        // Get the decoded path without the query string, e.g. `/app/uploads/2026/04/Barley-300x200.png`.
        $path = rawurldecode(strtok($_SERVER['REQUEST_URI'], '?'));
        $file = wp_basename($path);

        // Make it an absolute URL for `attachment_url_to_postid`.
        // Use the local uploads URL, as WordPress can resolve it without relying on WP Offload Media.
        $absolute_url = get_home_url(null, $path);

        // Get the attachment id from the url.
        $attachment_id = attachment_url_to_postid($absolute_url);
        $size = 'full';

        // Sized image URLs, e.g. `Barley-300x200.png`, are resolved via the full size URL, e.g. `Barley.png`.
        if (!$attachment_id && preg_match('/^(.+)-\d+x\d+(\.[a-z0-9]+)$/i', $absolute_url, $matches)) {
            // Large uploads are stored as e.g. `Barley-scaled.png`, but their sizes are named after `Barley.png`.
            $attachment_id = attachment_url_to_postid($matches[1] . $matches[2])
                ?: attachment_url_to_postid($matches[1] . '-scaled' . $matches[2]);

            $sizes = $attachment_id ? (wp_get_attachment_metadata($attachment_id)['sizes'] ?? []) : [];
            $matching_sizes = is_array($sizes) ? array_filter($sizes, fn($s) => ($s['file'] ?? '') === $file) : [];

            // If the size no longer exists, e.g. after a theme change, fall back to the full size.
            $size = array_key_first($matching_sizes) ?? 'full';
        }

        if (!$attachment_id) {
            return;
        }

        // Get the url from the attachment id.
        $cdn_url = $size === 'full' ? wp_get_attachment_url($attachment_id) : wp_get_attachment_image_url($attachment_id, $size);

        // Don't redirect to a local url, e.g. if the attachment hasn't been offloaded.
        if (!$cdn_url || str_contains($cdn_url, '/app/uploads/')) {
            return;
        }

        // Only redirect permanently to the requested file. Another file, e.g. the full size because the
        // requested size is missing or wasn't offloaded, may change, so don't let browsers cache the redirect.
        $status = rawurldecode(wp_basename(parse_url($cdn_url, PHP_URL_PATH))) === $file ? 301 : 302;

        // Redirect to the CDN URL.
        wp_redirect($cdn_url, $status);
        exit;
    }
}

new AmazonS3AndCloudFrontTweaks();
