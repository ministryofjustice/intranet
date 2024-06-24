<?php
// ---------------------------------------------
// Functions to improve the security of the site
// ---------------------------------------------

// Prevents WordPress from "guessing" URLs
use Roots\WPConfig\Config;

function no_redirect_on_404($redirect_url)
{
    if (is_404()) {
        return false;
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'no_redirect_on_404');

// Disable xmlrpc
add_filter('xmlrpc_enabled', '__return_false');

// Disable pingbacls
function remove_x_pingback($headers)
{
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'remove_x_pingback');

// Remove autocomplete for password on wp-login.php
function acme_autocomplete_login_init()
{
    ob_start();
}
add_action('login_init', 'acme_autocomplete_login_init');
function acme_autocomplete_login_form()
{
    $content = ob_get_contents();
    ob_end_clean();

    $content = str_replace('id="loginform"', 'id="loginform" autocomplete="off"', $content);
    $content = str_replace('id="user_pass"', 'id="user_pass" autocomplete="off"', $content);

    echo $content;
}
add_action('login_form', 'acme_autocomplete_login_form');

// Force logout after x hours
function control_login_period($expirein)
{
    return 180 * DAY_IN_SECONDS; // Cookies set to expire in 180 days.
}
add_filter('auth_cookie_expiration', 'control_login_period');

/**
 * Handle loopback requests.
 *
 * Handle requests to the application host, by sending them to the loopback url.
 *
 * @param false|array|WP_Error $response
 * @param array $parsed_args
 * @param string $url
 * @return false|array|WP_Error
 */
add_filter('pre_http_request', function (false|array|WP_Error $response, array $parsed_args, string $url): false|array|WP_Error
{
    // Is the request url to the application host?
    if (parse_url($url, PHP_URL_HOST) !== parse_url(get_home_url(), PHP_URL_HOST)) {
        return $response;
    }

    // Replace the URL.
    $new_url = str_replace(get_home_url(), 'http://localhost:8080', $url);

    // We don't need to verify ssl, calling a trusted container.
    $parsed_args['sslverify'] = false;

    // Get an instance of WP_Http.
    $http = _wp_http_get_object();

    // Return the result.
    return $http->request($new_url, $parsed_args);
}, 10, 3);

