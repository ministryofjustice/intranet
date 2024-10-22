<?php

namespace DeliciousBrains\WP_Offload_Media\Tweaks;

/**
 * Amazon S3 and CloudFront - assets.
 * 
 * This class contains functions related to serving build assets via Amazon S3 and CloudFront.
 */

class AmazonS3AndCloudFrontAssets
{
    private mixed $cloudfront_host;
    private string $cloudfront_asset_url;

    public function __construct()
    {
        // Get the CloudFront host.
        $this->cloudfront_host =  $_ENV['AWS_CLOUDFRONT_HOST'];
        // Set the scheme/protocol for CloudFront, default to https.
        $cloudfront_scheme = isset($_ENV['AWS_CLOUDFRONT_SCHEME']) && $_ENV['AWS_CLOUDFRONT_SCHEME'] === 'http' ? 'http' : 'https';
        // Set the CloudFront asset URL.
        $this->cloudfront_asset_url = $cloudfront_scheme . '://' . $this->cloudfront_host . '/build/' . $_ENV['IMAGE_TAG'];

        // URL Rewriting.
        add_filter('style_loader_src', [$this, 'rewriteSrc'], 10, 2);
        add_filter('script_loader_src', [$this, 'rewriteSrc'], 10, 2);
        add_filter('wp_resource_hints', [$this, 'registerResourceHints'], 10, 2);
    }

    /**
     * Rewrite the URL of assets to be served via the CDN.
     * 
     * @param string $src
     * @param string $handle
     *
     * @return string
     */

    public function rewriteSrc(string $src, string $handle): string
    {
        // If the host is not the same as WP_HOME, then return early.
        if (parse_url($src, PHP_URL_HOST) !== parse_url(get_home_url(), PHP_URL_HOST)) {
            return $src;
        }

        // Fonts cannot be served via the CDN, because browsers do not send cookies for `@font-face` requests.
        // Exclude the core-css and style handles from being rewritten, because they use `@font-face`.
        if (in_array($handle, ['core-css', 'style'])) {
            return $src;
        }

        return str_replace(get_home_url(), $this->cloudfront_asset_url, $src);
    }

    /**
     * Register a DNS prefetch tag for the pull domain if rewriting is enabled.
     *
     * @param array  $hints
     * @param string $relation_type
     *
     * @return array
     */

    public function registerResourceHints(array $hints, string $relation_type): array
    {
        if ('dns-prefetch' === $relation_type) {
            $hints[] = '//cdn.intranet.docker';
        }

        return $hints;
    }
}

new AmazonS3AndCloudFrontAssets();
