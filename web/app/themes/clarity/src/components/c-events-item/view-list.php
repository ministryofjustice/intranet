<?php
if (! defined('ABSPATH')) {
    die();
}

if (is_array($events)) {
    // Limit events listed on page to two for homepage display
    if (is_front_page()) {
        $events = array_splice($events, 0, 3);
    }

    // Limit events listed on page team homepage template
    if (is_singular('team_pages')) {
        $events = array_splice($events, 0, 4);
    }

    foreach ($events as $key => $post) {
        $event_id    = $post->ID;
        $post_url    = $post->url;
        $event_title = $post->post_title;
        $start_time  = $post->event_start_time;
        $end_time    = $post->event_end_time;
        $start_date  = $post->event_start_date;
        $end_date    = $post->event_end_date;
        $location    = $post->event_location;
        $date        = $post->event_start_date;
        $year        = date('Y', strtotime($start_date));
        $month       = date('M', strtotime($start_date));
        $day         = date('l', strtotime($start_date));
        $all_day     = $post->event_allday;

        if ($all_day === true) {
            $all_day = 'all_day';
        }

        // Adds class for homepage display
        if (is_front_page()) {
            echo '<div class="c-events-item-homepage">';
        } elseif (is_singular('team_pages')) {
            echo '<div class="c-team-homepage">';
        } else {
            echo '<div class="c-events-item-list">';
        }

        include locate_template('src/components/c-calendar-icon/view.php');
        include locate_template('src/components/c-events-item-byline/view.php');
        echo '</div>';
    }
}
