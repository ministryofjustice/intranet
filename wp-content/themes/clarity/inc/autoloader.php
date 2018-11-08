<?php

if (!defined('ABSPATH')) {
    die();
}

/**
* Autoloader. Needs to be replaced with a PSR4 compatible autoloader eventually.
*/
spl_autoload_register('moj_autoload');

function moj_autoload($cls)
{
    $cls = ltrim($cls, '\\');

    if (strpos($cls, 'MOJ\Intranet') !== 0) {
        return;
    }

    $cls = str_replace('MOJ\Intranet', '', $cls);
    $cls = strtolower($cls);

    $path = get_stylesheet_directory() . '/inc' .
        str_replace('\\', DIRECTORY_SEPARATOR, $cls)  . '.php';

    require_once($path);
}
