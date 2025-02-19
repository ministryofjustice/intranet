<?php

use MOJ\Intranet\Agency as Agency;

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
        'httponly' => false
    ];

    // use only on HTTPS - browser redirects on a loop otherwise
    if (!empty($_SERVER["HTTPS"])) {
        $options['secure'] = true;
    }
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
    $url = get_home_url(1, '/agency-switcher');
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
    $options = [
        'path' => COOKIEPATH,
        'domain' => parse_url(get_home_url(), PHP_URL_HOST),
        'httponly' => true
    ];

    // use only on HTTPS - browser redirects on a loop otherwise
    if (!empty($_SERVER["HTTPS"])) {
        $options['secure'] = true;
    }

    $options['expires'] = ($active ? strtotime('+7 days') : 1);

    setcookie('dw_can_edit_posts', ($active ? 1 : 0), $options);
}
