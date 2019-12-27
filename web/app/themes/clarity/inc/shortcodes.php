<?php

add_filter('the_content', 'fix_shortcode_p_gaps');

function fix_shortcode_p_gaps($content = null)
{
    $block = join('|', array( 'mostpopular', 'dw_col' ));
    $rep   = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", '[$2$3]', $content);
    $rep   = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", '[/$2]', $rep);
    return $rep;
}

add_shortcode('mostpopular', 'hr_most_popular_shortcode');

function hr_most_popular_shortcode()
{
    ob_start();
    get_template_part('src/components/c-most-popular/view');
    return ob_get_clean();
}

// Allows editors to create two columns in pages
add_shortcode('columns', 'clarity_columns_shortcode');

function clarity_columns_shortcode($atts, $content = '')
{
    return '<div class="l-column-wrapper">' . apply_filters('the_content', $content) . '</div>';
}

add_shortcode('col', 'clarity_col_shortcode');

function clarity_col_shortcode($atts, $content = '')
{
    return '<div class="l-column-half-section">' . apply_filters('the_content', $content) . '</div>';
}
