<?php

/**
 * Modifications to adapt the wp-sentry plugin.
 * 
 * @package Clarity
 */

namespace MOJ\Intranet;

if (!defined('ABSPATH')) {
    die();
}

class WpSentry
{

    public function __construct()
    {
        $this->addHooks();
    }

    public function addHooks()
    {
        add_filter('wp_sentry_public_options', [$this, 'filterSentryJsOptions']);
        error_log('added wp_sentry_public_options hook');
    }

    /**
     * Filter the options used by sentry-javascript for `Sentry.init()`
     */

    public function filterSentryJsOptions(array $options)
    {
        // If we're not on an admin or preview screen, then return early.
        if (!(is_admin() || is_preview())) {
            return $options;
        }

        // We are either on an admin screen or a preview screen.
        // Add custom settings for admin screens.
        return array_merge($options, array(
            'sendDefaultPii' => true,
            'sampleRate' => 1,
            'tracesSampleRate' => 1,
            'replaysSessionSampleRate' => 1,
            'replaysOnErrorSampleRate' => 1,
            'wpSessionReplayOptions' => [
                // To capture additional information such as request and response headers or bodies,
                // you'll need to opt-in via networkDetailAllowUrls
                'networkDetailAllowUrls' => [get_home_url()],
            ]
        ));
    }
}

new WpSentry();
