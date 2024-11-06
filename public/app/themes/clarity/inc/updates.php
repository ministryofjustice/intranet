<?php

namespace MOJ\Justice;

/**
 * Functions to related to updates, and disabling checks.
 */
class Updates
{
    public function __construct()
    {
        $this->hooks();
    }

    /**
     * @return void
     */
    public function hooks(): void
    {
        /**
         * Prevent http requests to api.wordpress.org to check for WP core versions.
         * 
         * Even though `AUTOMATIC_UPDATER_DISABLED` is set to true, we need to block
         * requests api.wordpress.org to where WP checks for available versions.
         * 
         * This filter's function returns a dummy payload that will prevent an api request, 
         * and not cause an error.
         * 
         * @see https://wp-kama.com/2020/disable-wp-updates-check
         * @see https://developer.wordpress.org/reference/functions/wp_version_check/
         */
        add_filter(
            'pre_site_transient_update_core',
            fn() =>  (object) [
                'updates' => [],
                'version_checked' => $GLOBALS['wp_version'],
                'last_checked' => time()
            ]
        );

        /**
         * Prevent http requests to api.wordpress.org to check for theme version.
         * 
         * This filter's function returns a dummy payload that will prevent an api request,
         * and not cause an error.
         * 
         * @see https://wp-kama.com/2020/disable-wp-updates-check
         * @see https://developer.wordpress.org/reference/functions/wp_update_themes/
         */
        add_filter('pre_site_transient_update_themes', static function ($value) {
            static $theme;

            $theme || $theme = wp_get_theme('clarity');

            return (object) [
                'last_checked' => time(),
                'checked' => [
                    'clarity' => $theme->get('Version')
                ]
            ];
        });

        /**
         * Prevent http requests to api.wordpress.org to check for plugin versions.
         * 
         * This filter's function returns a dummy payload that will prevent an api request,
         * and not cause an error.
         * 
         * @see https://wp-kama.com/2020/disable-wp-updates-check
         * @see https://developer.wordpress.org/reference/functions/wp_update_plugins/
         */
        add_filter('pre_site_transient_update_plugins', static function ($value) {
            static $plugins;
            $plugins || $plugins = get_plugins();

            $return_value = (object) [
                'last_checked' => time(),
                'checked' => []
            ];

            foreach ($plugins as $file => $p) {
                $return_value->checked[$file] = $p['Version'];
            }

            return $return_value;
        });
    }
}

new Updates();
