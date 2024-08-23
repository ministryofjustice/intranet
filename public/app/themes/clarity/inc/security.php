<?php

namespace MOJ\Justice;

use Roots\WPConfig\Config;

// ---------------------------------------------
// Functions to improve the security of the site
// ---------------------------------------------

/**
 * Add a little security for WordPress
 */
class Security
{
    /**
     * Loads up actions that are called when WordPress initialises
     */
    public function __construct()
    {
        $this->actions();
    }

    /**
     * @return void
     */
    public function actions(): void
    {
        // no generator meta tag in the head
        remove_action('wp_head', 'wp_generator');

        add_filter('redirect_canonical', [$this, 'noRedirect404']);
        add_filter('xmlrpc_enabled', '__return_false');
        add_filter('wp_headers', [$this, 'headerMods']);
        add_filter('auth_cookie_expiration', [$this, 'setLoginPeriod'], 10, 0);
        add_filter('pre_http_request', [$this, 'handleLoopbackRequests'], 10, 3);
    }

    /**
     * Prevent WordPress from trying to guess and redirect a 404 page
     *
     * https://developer.wordpress.org/reference/functions/redirect_canonical/
     *
     * @param $redirect_url
     *
     * @return false|mixed
     */
    public function noRedirect404($redirect_url): mixed
    {
        if (is_404()) {
            return false;
        }

        return $redirect_url;
    }

    /**
     * @param $headers
     *
     * @return mixed
     */
    public function headerMods($headers): mixed
    {
        unset($headers['X-Pingback']);

        $headers['X-Powered-By'] = 'Justice Digital';
        return $headers;
    }

    /**
     * Sets the expiration time of the login session cookie
     *
     * Nb. if we can harden access to the login page this value
     * can be extended to a much longer period
     *
     * @return float|int
     */
    public function setLoginPeriod(): float|int
    {
        return 7 * DAY_IN_SECONDS; // Cookies set to expire in 7 days.
    }

    /**
     * Handle loopback requests.
     *
     * Handle requests to the application host, by sending them to the loopback url.
     *
     * @param false|array|\WP_Error $response
     * @param array $parsed_args
     * @param string $url
     * @return false|array|\WP_Error
     */

    public function handleLoopbackRequests(false|array|\WP_Error $response, array $parsed_args, string $url): false|array|\WP_Error
    {
        // Is the request url to the application host?
        if (parse_url($url, PHP_URL_HOST) !== parse_url(get_home_url(), PHP_URL_HOST)) {
            // Request is not to the application host - log the url.
            error_log('pre_http_request url: ' . $url);
            return $response;
        }

        if (isset($parsed_args['keep_home_url'])) {
            return $response;
        }

        // Replace the URL.
        $new_url = str_replace(get_home_url(), Config::get('LOOPBACK_URL'), $url);

        // We don't need to verify ssl, calling a trusted container.
        $parsed_args['sslverify'] = false;

        // Get an instance of WP_Http.
        $http = _wp_http_get_object();

        // Return the result.
        return $http->request($new_url, $parsed_args);
    }
}

new Security();
