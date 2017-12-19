<?php

if (!defined('ABSPATH')) {
    die();
}

/**
 * Returns any text you supply in lower-case and hyphenated
 * @string the string to convert
 * @return slugified string
 */

function slugify($string)
{
    $newstring = str_replace(' ', '-', $string);
    $newstring = strtolower($newstring);
    return $newstring;
}
/***
 *
 * Next and previous function used on all list/archive pages, ie, blogs, news.
 *
 ***/
add_action('wp_head', 'feedback_form');
add_filter('next_posts_link_attributes', 'posts_link_attributes_prev');
add_filter('previous_posts_link_attributes', 'posts_link_attributes_next');

function posts_link_attributes_prev()
{
    return 'class="c-pagination__link c-pagination__link--prev"';
}
function posts_link_attributes_next()
{
    return 'class="c-pagination__link c-pagination__link--next"';
}

add_filter('get_archives_link', 'custom_monthly_archive', 10, 6);

function custom_monthly_archive($link_html, $url, $text, $format)
{
    if ('custom' === $format) {
        $strip_url = str_replace('http://intranet.docker/blog/', '', $url);
    }
    $link_html = '<option value='.$strip_url.'>'.$text.'</option>';
    return $link_html;
}
