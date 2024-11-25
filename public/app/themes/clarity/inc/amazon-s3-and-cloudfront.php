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

        // Redirect legacy URLs to cdn URLs.
        add_action('template_redirect', [$this, 'maybeRedirect404s']);
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
        if (false === strpos($_SERVER['REQUEST_URI'], '/app/uploads/')) {
            return;
        }

        // Replace '/app/uploads/' with '/media/'.
        $media_uri = str_replace('/app/uploads/', '/media/', $_SERVER['REQUEST_URI']);

        // Make it an absolute URL for `attachment_url_to_postid`.
        $absolute_url = get_home_url(null, $media_uri);

        // Get the attachment id from the url.
        $attachment_id = attachment_url_to_postid($absolute_url);

        if (!$attachment_id) {
            return;
        }

        // Get the url from the attachment id.
        $cdn_url = wp_get_attachment_url($attachment_id);

        if (!$cdn_url) {
            return;
        }

        // Redirect to the CDN URL.
        wp_redirect($cdn_url, 301);
        exit;
    }
}

new AmazonS3AndCloudFrontTweaks();
