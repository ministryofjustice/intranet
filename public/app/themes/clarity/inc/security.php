<?php

namespace MOJ\Justice;

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
}

new Security();
