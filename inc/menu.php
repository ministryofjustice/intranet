<?php

if (!defined('ABSPATH')) {
    die();
}

// Register action/filter callbacks
add_action('init', 'register_my_menu');

/*
* Registers all menus used throughout site for all agencies.
* Any other menu not here, is registered in the old template in menu-locations.php.
*/
// Register navigation menus
function register_my_menu()
{
    register_nav_menu('header-menu', __('Header Menu'));
    register_nav_menu('hq-menu', __('HQ Menu'));
    register_nav_menu('cica-menu', __('CICA Menu'));
    register_nav_menu('hmcts-menu', __('HMCTS Menu'));
    register_nav_menu('judicial-appointments-commission-menu', __('JAC Menu'));
    register_nav_menu('judicial-office-menu', __('JO Menu'));
    register_nav_menu('law-commission-menu', __('LawCom Menu'));
    register_nav_menu('laa-menu', __('LAA Menu'));
    register_nav_menu('noms-menu', __('NOMS Menu'));
    register_nav_menu('nps-menu', __('NPS Menu'));
    register_nav_menu('opg-menu', __('OPG Menu'));
    register_nav_menu('ospt-menu', __('OSPT Menu'));
    register_nav_menu('pb-menu', __('PB Menu'));
    register_nav_menu('ppo-menu', __('PPO Menu'));
}
