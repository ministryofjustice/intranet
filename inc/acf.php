<?php
/**
 * Pick up ACF fields from parent theme
 */
add_filter('acf/settings/load_json', 'my_acf_json_load_point');

function my_acf_json_load_point($paths)
{
    // append path
    $paths[] = get_template_directory() . '/acf-json';

    // return
    return $paths;
}

/***
*
* New option page for header banner - ACF options
* https://www.advancedcustomfields.com/resources/acf_add_options_page/
*
***/

if (function_exists('acf_add_options_page')) {
    acf_add_options_page('Phase Banner');
}
