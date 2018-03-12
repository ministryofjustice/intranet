<?php
use MOJ\Intranet\Agency;

if (!defined('ABSPATH')) {
    die();
}
/*
add_action('wp_ajax_get_news_api', 'get_events_api');
add_action('wp_ajax_nopriv_get_news_api', 'get_events_api');

function get_events_api($taxonomy, $tax_id = false)
{
    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();
    $siteurl = get_home_url();
    $agency_name = $activeAgency['wp_tag_id'];

    if ($taxonomy === 'search') {
        $response = wp_remote_get($siteurl.'/wp-json/intranet/v2/future-events/'.$agency_name);
    } elseif ($taxonomy === 'region') {
        $response = wp_remote_get($siteurl.'/wp-json/intranet/v2/region-events/'.$agency_name.'/'.$tax_id.'/');
    } elseif ($taxonomy === 'campaign') {
        $response = wp_remote_get($siteurl.'/wp-json/intranet/v2/campaign-events/'.$agency_name.'/'.$tax_id.'/');
    }

    if (is_wp_error($response)) {
        return;
    }

    $pagetotal = wp_remote_retrieve_header($response, 'x-wp-totalpages');
    $posts = json_decode(wp_remote_retrieve_body($response), true);
    $response_code = wp_remote_retrieve_response_code($response);
    $response_message = wp_remote_retrieve_response_message($response);

    if (200 == $response_code && $response_message == 'OK') {
        echo '<div class="data-type" data-type="event"></div>';

        foreach ($posts['events'] as $key => $post) {
            $start_date = get_post_meta($id, '_event-start-date', true);
            $end_date = get_post_meta($id, '_event-end-date', true);

            $start_time = $post['event_start_time'];
            $end_time = $post['event_end_time'];
            $start_date = $post['event_start_date'];
            $end_date = $post['event_end_date'];
            $all_day = $post['all_day'];
            $location = $post['event_location'];
            $date = $post['event_start_date'];
            $year = date('Y', strtotime($start_date));
            $month = date('M', strtotime($start_date));
            $day = date('F', strtotime($start_date));

            // If the user selects start date and end date that are the same, just display one date.
            if ($start_date === $end_date) {
                $date = date("d M", strtotime($start_date));
            } else {
                $date = date("d M", strtotime($start_date)) . ' - ' . date("d M", strtotime($end_date));
            } ?>
                <article class="c-events-item-aside" data-type="event">
                  <?php
                  // include() rather then get_template_part() so that above variables are passed.
                  //include(locate_template('src/components/c-calendar-icon/view-post.php'));
                  //include(locate_template('src/components/c-events-item-byline/view.php'));
                  ?>
                </article>
            <?php

        }
    }
}
