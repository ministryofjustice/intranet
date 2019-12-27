<?php

/**
 *
 * Enqueue Clarity scripts and styles.
 */

add_action('wp_enqueue_scripts', 'enqueue_clarity_scripts', 99);

function enqueue_clarity_scripts()
{
    define('MOJ_ENQUEUE_PATH', get_template_directory_uri() . '/dist');

    // CSS
    wp_enqueue_style('core-css', mix_asset('/css/globals.css'), array(), null, 'all');
    wp_enqueue_style('style', mix_asset('/css/style.css'), array(), null, 'screen');
    wp_enqueue_style('ie', mix_asset('/css/style.ie.css'), array(), null, 'screen');
    wp_enqueue_style('ie8', mix_asset('/css/style.ie8.css'), array(), null, 'screen');
    wp_enqueue_style('print', mix_asset('/css/style.print.css'), array(), null, 'print');

    // JS and jQuery
    wp_enqueue_script('core-js', mix_asset('/js/main.js'), array('jquery'), null, true);
    wp_localize_script('core', 'myAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);

    // Third party vendor scripts
    wp_deregister_script('jquery'); // This removes jquery shipped with WP so that we can add our own.
    wp_enqueue_script('jquery', mix_asset('/js/jquery.min.js'));

    wp_enqueue_script('popup', mix_asset('/js/magnific-popup.js'), array('jquery'));
    wp_enqueue_script('html5shiv', mix_asset('/js/ie8-js-html5shiv.js'));
    wp_enqueue_script('respond', mix_asset('/js/respond.min.js'));
    wp_enqueue_script('selectivizr', mix_asset('/js/selectivizr-min.js'));

    // conditionals
    wp_style_add_data('ie', 'conditional', 'IE 7');
    wp_style_add_data('ie8', 'conditional', 'IE 8');
    wp_style_add_data('respond', 'conditional', 'lt IE 9');
    wp_style_add_data('html5shiv', 'conditional', 'lt IE 9');
}

/**
 * @param $filename
 * @return string
 */
function mix_asset($filename)
{
    $manifest_path = MOJ_ENQUEUE_PATH . '/mix-manifest.json';
    $manifest = json_decode(file_get_contents($manifest_path), true);
    if (!isset($manifest[$filename])) {
        error_log("Mix asset '$filename' does not exist in manifest.");
    }
    return MOJ_ENQUEUE_PATH . $manifest[$filename];
}

/**
 *
 * Remove Gutenberg CSS as we do not use
 */
add_action('wp_print_styles', 'wps_deregister_gutenberg_css', 100);

function wps_deregister_gutenberg_css()
{
    wp_dequeue_style('wp-block-library');
    wp_deregister_style('wp-block-library');
}

/**
 *
 * Enqueued backend admin CSS and JS
 */
add_action('admin_enqueue_scripts', 'clarity_admin_enqueue');

function clarity_admin_enqueue($hook)
{
    // Warning message to editors when they don't enter a page title
    if ($hook == 'post-new.php' || $hook == 'post.php') :
        wp_enqueue_script(
            'force_title_script',
            get_stylesheet_directory_uri() . '/inc/admin/js/force-title.js',
            array(),
            null,
            false
        );
        wp_enqueue_script(
            'colour-contrast-checker',
            get_stylesheet_directory_uri() . '/inc/admin/js/colour-contrast-checker.js',
            array(),
            null,
            false
        );
        wp_localize_script('colour-contrast-checker', 'myAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);

        wp_register_style(
            'page-search-dropdown-filter',
            get_stylesheet_directory_uri() . '/inc/admin/css/page-search-dropdown-filter.css',
            array(),
            '0.2.0',
            'all'
        );
        wp_enqueue_style('page-search-dropdown-filter');
    endif;
}
