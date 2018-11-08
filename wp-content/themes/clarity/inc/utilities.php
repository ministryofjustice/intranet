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
//add_action('wp_head', 'feedback_form');
//add_filter('next_posts_link_attributes', 'posts_link_attributes_prev');
//add_filter('previous_posts_link_attributes', 'posts_link_attributes_next');

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
        $siteurl = get_home_url() . '/blog/';
        $strip_url = str_replace($siteurl, '', $url);

        $replace_with_slash = str_replace('/', '-', $strip_url);

        $valueSelected = $replace_with_slash . '01T00:00:00';

        /* $valueSelected returns 'http:--intranet.docker-2017-12-01T00:00:00' a sting that is not parsed in any usable format.
        * preg_match strips it to the values the variables below require.
        * TODO find out why is was orgnially parsed in this way and then why it stopped working.
        */

        $subject = $valueSelected;
        $pattern = '/[\d]{4}-[\d]{2}-[\d]{2}[\D]0{2}:0{2}:0{2}$/';
        /* Example string 'http:--intranet.docker-2017-12-01T00:00:00' . We want only the last part from the year onward.
        */

        preg_match($pattern, $subject, $match);

        $valueSelected = $match[0];

        $get_year = substr($valueSelected, 0, 4);
        $get_month = substr($valueSelected, 5, 2);

        if ($get_month == 12) {
            $year = $get_year + 1;
            $month = '01';
            $date_range = '&after='.$valueSelected.'&before='.$year.'-'.$month.'-01T00:00:00';
            $link_html = '<option value='.$date_range.'>'.$text.'</option>';
        } else {
            $year = $get_year;
            $month = $get_month + 1;

            $add_leading_zero = str_pad($month, 2, '0', STR_PAD_LEFT);

            $date_range = '&after='.$valueSelected.'&before='.$year.'-'.$add_leading_zero.'-01T00:00:00';
            $link_html = '<option value='.$date_range.'>'.$text.'</option>';
        }
    }

    return $link_html;
}
