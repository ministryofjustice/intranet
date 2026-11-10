<?php

use MOJ\Intranet\Agency as Agency;
use Roots\WPConfig\Config;

/**
 * Set the intranet cookie if GET variables are passed
 */
add_action('wp', function () {
    $agency_default = 'hq';
    $agency_param = $_GET['agency'] ?? false;
    $agencies = new Agency();

    $options = [
        'expires' => time() + (3650 * DAY_IN_SECONDS),
        'path' => COOKIEPATH,
        'domain' => COOKIE_DOMAIN,
        'httponly' => false,
        // use only on HTTPS - browser redirects on a loop otherwise
        'secure' => is_ssl()
    ];

    // If the agency param is set, set the dw_agency cookie and return
    if ($agency_param) {
        $agency = trim($agency_param);

        // If the agency is not valid, set it to the default agency ('hq')
        if (!$agencies->agencyExists($agency)) {
            $agency = $agency_default;
        }
        // set a cookie with an agency defined by the user
        setcookie('dw_agency', $agency, $options);
        $_COOKIE['dw_agency'] = $agency;
        // Return
        return;
    }
    // Otherwise, check if the agency cookie is already set
    $agency = $_COOKIE['dw_agency'] ?? '';
    $slug = get_post_field('post_name');

    if (
        // If the agency cookie is set and is a valid agency
        $agencies->agencyExists($agency) ||
        // Or the current page is the agency switcher, privacy notice, or accessibility page
        in_array($slug, ['agency-switcher', 'privacy-notice', 'accessibility'], true) ||
        // Or the user is logged in
        is_user_logged_in() ||
        // Or we are in the admin area
        (is_admin() || wp_doing_ajax() || wp_doing_cron())
    ) {
        // Do nothing and return
        return;
    }
    $url = '/agency-switcher';
    // Set the send_back param so that we can send the user back to the current page after selecting an agency
    $url = add_query_arg(['send_back' => urlencode(get_permalink())], $url);
    // Redirect to the agency switcher page
    wp_safe_redirect($url);
    exit;
});

/**
 * Check if the user has permission to edit posts, and set or delete the
 * 'edit posts' cookie accordingly.
 *
 * Logic: $can_edit = current_user_can
 * If ($can_edit = true) = dw_set_edit_posts_cookie(true);
 * If ($can_edit = false || isset = true) = dw_set_edit_posts_cookie(false);
 * If ($can_edit = false || isset = false) = null;
 *
 * Action: init
 */
add_action('init', function () {
    $can_edit = current_user_can('edit_posts');

    if ($can_edit || isset($_COOKIE['dw_can_edit_posts'])) {
        dw_set_edit_posts_cookie($can_edit);
    }
}, 10);


/**
 * Check if the user has permission to edit posts, and set or delete the
 * 'edit posts' cookie accordingly.
 *
 * This runs after wp_login, at which point current_user_can() cannot be used.
 *
 * Action: wp_login
 *
 * @param string $user_login
 * @param WP_User $user
 */
add_action('wp_login', function ($user_login, WP_User $user) {
    dw_set_edit_posts_cookie(!empty($user->allcaps['edit_posts']));
}, 10, 2);

/**
 * Set or delete the 'edit posts' cookie.
 *
 * @param bool $active To set the cookie, or not to set the cookie. That is the question.
 */
function dw_set_edit_posts_cookie(bool $active): void
{
    // Return if doing cron
    if (wp_doing_cron()) {
        return;
    }

    $options = [
        'path' => COOKIEPATH,
        'domain' => parse_url(get_home_url(), PHP_URL_HOST),
        'httponly' => true,
        // use only on HTTPS - browser redirects on a loop otherwise
        'secure' => is_ssl()
    ];

    $options['expires'] = ($active ? strtotime('+7 days') : 1);

    setcookie('dw_can_edit_posts', ($active ? 1 : 0), $options);
}


/**
 * Set or delete the moj_config cookie, for users who can edit posts.
 *
 * The ingress config reads this cookie, its value must be MOJ_CONFIG.
 * The cookie is only sent when its value needs to change.
 *
 * Action: init
 */
add_action('init', function () {
    moj_set_config_cookie(current_user_can('edit_posts'));
}, 10);


/**
 * This runs whenever WordPress sets the login cookie: login, re-login, and user switching.
 * Give the moj_config cookie the same browser expiry as the login cookie, so it does not
 * expire while an editor is still logged in. Otherwise the ingress would treat their next
 * request, e.g. a save or heartbeat POST, as from a non-editor.
 * $expire is 0 (a session cookie) without "Remember me", or the login expiry plus
 * WordPress's 12 hour grace period with it.
 *
 * Action: set_logged_in_cookie
 *
 * @param string $logged_in_cookie
 * @param int $expire When the browser should drop the login cookie, 0 for a session cookie.
 * @param int $expiration
 * @param int $user_id
 */
add_action('set_logged_in_cookie', function ($logged_in_cookie, $expire, $expiration, $user_id) {
    moj_set_config_cookie(user_can($user_id, 'edit_posts'), (int) $expire);
}, 10, 4);


/**
 * Delete the moj_config cookie on logout.
 *
 * Action: wp_logout
 */
add_action('wp_logout', function () {
    moj_set_config_cookie(false);
});


/**
 * Set or delete the moj_config cookie.
 *
 * @param bool $active To set the cookie, or delete it.
 * @param ?int $expires When the browser should drop the cookie, 0 for a session cookie.
 *                      If not passed, the cookie is only sent when missing, as a session cookie,
 *                      because the login cookie's expiry is not known here.
 */
function moj_set_config_cookie(bool $active, ?int $expires = null): void
{
    // Return if doing cron
    if (wp_doing_cron()) {
        return;
    }

    $secret = (string) Config::get('MOJ_CONFIG');
    $current = $_COOKIE['moj_config'] ?? null;

    // Never set an empty value, delete the cookie instead.
    $active = $active && $secret !== '';

    // Already set, unless a new expiry was passed in.
    if ($active && $expires === null && is_string($current) && hash_equals($secret, $current)) {
        return;
    }

    if (!$active && $current === null) {
        return;
    }

    $options = [
        'path' => COOKIEPATH,
        'domain' => parse_url(get_home_url(), PHP_URL_HOST),
        'httponly' => true,
        'secure' => is_ssl(),
        'samesite' => 'Lax',
        'expires' => ($active ? ($expires ?? 0) : 1),
    ];

    // Belt and braces: make sure a response that sets or deletes this cookie is never cached.
    // nginx already skips the cache for logged in users (server.conf), but it also ignores
    // Set-Cookie when caching (fastcgi_ignore_headers), so a cached copy would replay this
    // cookie to other users. X-Accel-Expires is an nginx header, not an HTTP standard:
    // nginx reads it from PHP as the number of seconds to cache the response, 0 means
    // do not cache, and it is removed before the response is sent to the browser.
    header('X-Accel-Expires: 0');

    setcookie('moj_config', ($active ? $secret : ''), $options);

    if ($active) {
        $_COOKIE['moj_config'] = $secret;
    } else {
        unset($_COOKIE['moj_config']);
    }
}
