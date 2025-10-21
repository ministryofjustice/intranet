<?php

namespace MOJ\Justice;

use function Env\env;
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
     * A list of known hosts.
     */
    private array $known_hosts = [
        'api.deliciousbrains.com',
        'connect.advancedcustomfields.com'
    ];

    /**
     * A list of known blocked hosts.
     */
    private array $blocked_hosts = [
        'totalsuite.net',          // Totalpoll Lite update check
        'collect.totalsuite.net', // Totalpoll Lite telemetry
    ];

    /**
     * The application host e.g. intranet.docker or intranet.justice.gov.uk
     */
    private string $home_host;

    /**
     * Loads up actions that are called when WordPress initialises
     */
    public function __construct()
    {
        $this->home_host = parse_url(get_home_url(), PHP_URL_HOST);

        $this->actions();

        // Push the application host to known_hosts.
        array_push($this->known_hosts, $this->home_host);

        // Push the OpenSearch host to known_hosts.
        if ($ep_url = Config::get('EP_HOST')) {
            array_push($this->known_hosts, parse_url($ep_url, PHP_URL_HOST));
        }

        // Push the S3 bucket host to known_hosts.
        if ($s3_bucket = env('AWS_S3_BUCKET')) {
            array_push($this->known_hosts, $s3_bucket . ".s3.eu-west-2.amazonaws.com");
        }

        if ($custom_s3_host = env('AWS_S3_CUSTOM_HOST')) {
            array_push($this->known_hosts, $custom_s3_host);
        }

        // Push the cache purge url host to known_hosts.
        $cache_purge_url = Config::get('NGINX_PURGE_CACHE_URL');
        if ($cache_purge_url) {
            array_push($this->known_hosts, parse_url($cache_purge_url, PHP_URL_HOST));
        }
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
        add_filter('pre_http_request', [$this, 'blockHostRequests'], 10, 3);
        add_filter('pre_http_request', [$this, 'logUnknownHostRequests'], 15, 3);
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
        if (parse_url($url, PHP_URL_HOST) !== $this->home_host) {
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

    /**
     * Block requests to known bad hosts.
     *
     * @param false|array|\WP_Error $response
     * @param array $parsed_args
     * @param string $url
     * @return false|array|\WP_Error
     */
    public function blockHostRequests(false|array|\WP_Error $response, array $parsed_args, string $url): false|array|\WP_Error
    {
        if (in_array(parse_url($url, PHP_URL_HOST), $this->blocked_hosts)) {
            return new \WP_Error('blocked_host', 'Requests to this host are blocked for security reasons.');
        }
        return $response;
    }

    /**
     * Log the urls of requests to unknown hosts.
     * 
     * This could be useful in identifying requests to malicious URLs.
     * 
     * @param false|array|\WP_Error $response
     * @param array $parsed_args
     * @param string $url
     * @return false|array|\WP_Error
     */
    public function logUnknownHostRequests(false|array|\WP_Error $response, array $parsed_args, string $url): false|array|\WP_Error
    {
        if (!in_array(parse_url($url, PHP_URL_HOST), $this->known_hosts)) {
            // Log the request url.
            error_log('pre_http_request url: ' . $url);
        }

        return $response;
    }
}

new Security();
