<?php

if (!defined('ABSPATH')) {
    die();
}

/**
 * Return the view for the component
 * @component [string] the folder name of the component
 * @data [array] the data to be passed to the component
 * @config [string, array] some components are reused, the config variable lets you pass info to them (see c-article-item for example)
 * @return true on success false on failure
 */

function get_component($component, $data = null, $config = null)
{
    $agency = get_intranet_code();
    $override = get_stylesheet_directory().'/views/components/'.$component.'/view-'.$agency.'.php';
    $global = get_stylesheet_directory().'/views/components/'.$component.'/view.php';

    if (file_exists($override)) {
        include($override);
        return true;
    } elseif (file_exists($global)) {
        include($global);
        return true;
    } else {
        return false;
    }
}
