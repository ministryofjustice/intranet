<?php

/***
 *
 * Set the intranet cookie if GET variables are passed
 */
add_action('wp', 'set_intranet_cookie');

function set_intranet_cookie()
{
    $default_agency = 'hq';

    if (isset($_GET['agency'])) {
        $agency_value = isset($_GET['agency']) ? trim($_GET['agency']) : $default_agency;
        setcookie('dw_agency', $agency_value, time() + ( 3650 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN);
        $_COOKIE['dw_agency'] = $agency_value;
    } elseif (! isset($_COOKIE['dw_agency'])) {
        setcookie('dw_agency', $default_agency, time() + ( 3650 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN);
        $_COOKIE['dw_agency'] = $default_agency;
    }
}

/**
 * Check if the user has permission to edit posts, and set or delete the
 * 'edit posts' cookie accordingly.
 *
 * Action: init
 */
function dw_set_cache_cookie()
{
    if (current_user_can('edit_posts')) {
        dw_set_edit_posts_cookie(true);
    } elseif (isset($_COOKIE['dw_can_edit_posts'])) {
        dw_set_edit_posts_cookie(false);
    }
}
add_action('init', 'dw_set_cache_cookie', 10);

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
function dw_set_login_cookie($user_login, WP_User $user)
{
    if (!empty($user->allcaps['edit_posts'])) {
        dw_set_edit_posts_cookie(true);
    } else {
        dw_set_edit_posts_cookie(false);
    }
}
add_action('wp_login', 'dw_set_login_cookie', 10, 2);

/**
 * Set or delete the 'edit posts' cookie.
 *
 * @param bool $active To set the cookie, or not to set the cookie. That is the question.
 */
function dw_set_edit_posts_cookie($active)
{
    $cookie_url = preg_replace('#^https?://#', '', get_home_url());

    if ($active) {
        setcookie('dw_can_edit_posts', 1, strtotime('+7 days'), COOKIEPATH, $cookie_url);
    } else {
        setcookie('dw_can_edit_posts', 0, 1, COOKIEPATH, $cookie_url);
    }
}
