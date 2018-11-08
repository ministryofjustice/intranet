<?php

if (!defined('ABSPATH')) {
    die();
}

/***
 *
 * Set the intranet cookie if GET variables are passed
 *
 ***/
add_action('init', 'set_intranet_cookie');

function set_intranet_cookie()
{
    $default_agency = 'hq';

    if (isset($_GET['agency'])) {
        $agency_value =  isset($_GET['agency']) ? trim($_GET['agency']) : $default_agency;
        setcookie('dw_agency', $agency_value, time()+ (3650 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
        $_COOKIE['dw_agency'] = $agency_value;
    } elseif (!isset($_COOKIE['dw_agency'])) {
        setcookie('dw_agency', $default_agency, time()+ (3650 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
        $_COOKIE['dw_agency'] = $default_agency;
    }
}
