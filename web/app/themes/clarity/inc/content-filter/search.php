<?php
use MOJ\Intranet\Agency;
use MOJ\Intranet\EventsHelper;

add_action('wp_ajax_load_events_filter_results', 'load_events_filter_results');
add_action('wp_ajax_nopriv_load_events_filter_results', 'load_events_filter_results');

function load_events_filter_results()
{

    if (! wp_verify_nonce($_POST['nonce_hash'], 'search_filter_nonce')) {
        exit('Access not allowed.');
    }

    $oAgency           = new Agency();
    $activeAgency      = $oAgency->getCurrentAgency();
    $agency_term_id       = $activeAgency['wp_tag_id'];
    $date_filter = sanitize_text_field($_POST['valueSelected']);
    $post_id           = get_the_ID();
    $query             = sanitize_text_field($_POST['query']);

    $filter_options = ['keyword_search' => $query];

    if ($date_filter != 'all') {
        $filter_options['date_filter'] = $date_filter;
    }

    $EventsHelper  = new EventsHelper();

    if (isset($_POST['termID'])) {
        $tax_id = sanitize_text_field($_POST['termID']);

        $filter_options['region_filter'] = $tax_id;

        $events = $EventsHelper->get_events($agency_term_id, $filter_options);
    } else {
        $events = $EventsHelper->get_events($agency_term_id, $filter_options);
    }

    if ($events) {
        echo '<div class="data-type" data-type="event"></div>';

        foreach ($events as $key => $event) :
            $event_id         = $event->ID;
            $post_url         = $event->url;
            $event_title      = $event->post_title;

            $start_date = $event->event_start_date;
            $end_date   = $event->event_end_date;
            $start_time = $event->event_start_time;
            $end_time   = $event->event_end_time;
            $location   = $event->event_location;
            $date       = $event->event_start_date;
            $day        = date('l', strtotime($start_date));
            $month      = date('M', strtotime($start_date));
            $year       = date('Y', strtotime($start_date));
            $all_day    = $event->event_allday;

            if ($all_day == true) {
                $all_day = 'all_day';
            }

            echo '<div class="c-events-item-list">';

            include locate_template('src/components/c-calendar-icon/view.php');

            include locate_template('src/components/c-events-item-byline/view.php');

            echo '</div>';
        endforeach;
    } else {
        echo 'No events found during this date range :(';
    }
    die();
}

add_action('wp_ajax_load_search_results', 'load_search_results');
add_action('wp_ajax_nopriv_load_search_results', 'load_search_results');

function load_search_results()
{

    if (! wp_verify_nonce($_POST['nonce_hash'], 'search_filter_nonce')) {
        exit('Access not allowed.');
    }

    $query         = sanitize_text_field($_POST['query']);
    $valueSelected = sanitize_text_field($_POST['valueSelected']);
    $postType      = sanitize_text_field($_POST['postType']);

    if (isset($_POST['newsCategoryValue'])) {
        $newsCategory_ID = sanitize_text_field($_POST['newsCategoryValue']);
    }

    $oAgency      = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $post_per_page = 'per_page=10';
    $search        = ( ! empty($query) ? '&search=' . $query : '' );
    $agency_name   = '&agency=' . $activeAgency['wp_tag_id'];
    if ($postType === 'regional_news') {
        $news_category_name = ( ! empty($newsCategory_ID) ? '&region=' . $newsCategory_ID : '' );
    } else {
        $news_category_name = ( ! empty($newsCategory_ID) ? '&news_category=' . $newsCategory_ID : '' );
    }

    /*
    * A temporary measure so that API calls do not get blocked by
    * changing IPs not whitelisted. All calls are within container.
    */
    $siteurl = 'http://127.0.0.1';

    $response = wp_remote_get($siteurl . '/wp-json/wp/v2/' . $postType . '/?' . $post_per_page . $agency_name . $valueSelected . $search . $news_category_name);

    $post_total = wp_remote_retrieve_header($response, 'x-wp-total');
    $posts      = json_decode(wp_remote_retrieve_body($response), true);

    $response_code    = wp_remote_retrieve_response_code($response);
    $response_message = wp_remote_retrieve_response_message($response);

    if (200 == $response_code && $response_message == 'OK') {
        echo '<div class="data-type" data-type="' . $postType . '"></div>';
        foreach ($posts as $key => $post) {
            include locate_template('src/components/c-article-item/view-news-feed.php');
        }
    }
    die();
}

add_action('wp_ajax_load_next_results', 'load_next_results');
add_action('wp_ajax_nopriv_load_next_results', 'load_next_results');

function load_next_results()
{
    if (! wp_verify_nonce($_POST['nonce_hash'], 'search_filter_nonce')) {
        ¦exit('Access not allowed.');
    }

    $nextPageToRetrieve = sanitize_text_field($_POST['nextPageToRetrieve']);
    $query              = sanitize_text_field($_POST['query']);
    $valueSelected      = sanitize_text_field($_POST['valueSelected']);
    $postType           = sanitize_text_field($_POST['postType']);

    if (isset($_POST['newsCategoryValue'])) {
        $newsCategory_ID = sanitize_text_field($_POST['newsCategoryValue']);
    }

    $oAgency      = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $post_per_page = 'per_page=10';
    $current_page  = '&page=' . $nextPageToRetrieve;

    $search             = ( ! empty($query) ? '&search=' . $query : '' );
    $agency_name        = '&agency=' . $activeAgency['wp_tag_id'];
    $news_category_name = ( ! empty($newsCategory_ID) ? '&news_category=' . $newsCategory_ID : '' );

    /*
    * A temporary measure so that API calls do not get blocked by
    * changing IPs not whitelisted. All calls are within container.
    */
    $siteurl = 'http://127.0.0.1';

    $response = wp_remote_get($siteurl . '/wp-json/wp/v2/' . $postType . '/?' . $post_per_page . $current_page . $agency_name . $valueSelected . $search . $news_category_name);

    $pagetotal = wp_remote_retrieve_header($response, 'x-wp-totalpages');

    $posts = json_decode(wp_remote_retrieve_body($response), true);

    $response_code    = wp_remote_retrieve_response_code($response);
    $response_message = wp_remote_retrieve_response_message($response);

    if (200 == $response_code && $response_message == 'OK') {
        echo '<div class="data-type" data-type="' . $postType . '"></div>';
        foreach ($posts as $key => $post) {
            include locate_template('src/components/c-article-item/view-news-feed.php');
        }
    }
    die();
}

add_action('wp_ajax_load_search_results_total', 'load_search_results_total');
add_action('wp_ajax_nopriv_load_search_results_total', 'load_search_results_total');

function load_search_results_total()
{
    if (! wp_verify_nonce($_POST['nonce_hash'], 'search_filter_nonce')) {
        exit('Access not allowed.');
    }

    $query         = $_POST['query'];
    $valueSelected = $_POST['valueSelected'];
    $postType      = $_POST['postType'];

    if (isset($newsCategory_ID)) {
        $newsCategory_ID = $_POST['newsCategoryValue'];
    }

    $oAgency      = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $post_per_page      = 'per_page=10';
    $search             = ( ! empty($query) ? '&search=' . $query : '' );
    $agency_name        = '&agency=' . $activeAgency['wp_tag_id'];
    $news_category_name = ( ! empty($newsCategory_ID) ? '&news_category=' . $newsCategory_ID : '' );

    /*
    * A temporary measure so that API calls do not get blocked by
    * changing IPs not whitelisted. All calls are within container.
    */
    $siteurl = 'http://127.0.0.1';

    $response   = wp_remote_get($siteurl . '/wp-json/wp/v2/' . $postType . '/?' . $post_per_page . $agency_name . $valueSelected . $search . $news_category_name);
    $post_total = wp_remote_retrieve_header($response, 'x-wp-total');

    echo $post_total . ' search results';

    die();
}

add_action('wp_ajax_load_page_total', 'load_page_total');
add_action('wp_ajax_nopriv_load_page_total', 'load_page_total');

function load_page_total()
{
    if (! wp_verify_nonce($_POST['nonce_hash'], 'search_filter_nonce')) {
        exit('Access not allowed.');
    }

    $nextPageToRetrieve = sanitize_text_field($_POST['nextPageToRetrieve']);
    $query              = sanitize_text_field($_POST['query']);
    $valueSelected      = sanitize_text_field($_POST['valueSelected']);
    $postType           = sanitize_text_field($_POST['postType']);

    if (isset($_POST['newsCategoryValue'])) {
        $newsCategory_ID = sanitize_text_field($_POST['newsCategoryValue']);
    }

    $oAgency      = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $post_per_page           = 'per_page=10';
    $current_page            = '&page=' . $nextPageToRetrieve;
    $search                  = '&search=' . $query;
    $agency_name             = '&agency=' . $activeAgency['wp_tag_id'];
    $news_category_name      = ( ! empty($newsCategory_ID) ? '&news_category=' . $newsCategory_ID : '' );

    /*
    * A temporary measure so that API calls do not get blocked by
    * changing IPs not whitelisted. All calls are within container.
    */
    $siteurl = 'http://127.0.0.1';

    $response = wp_remote_get($siteurl . '/wp-json/wp/v2/' . $postType . '/?' . $post_per_page . $current_page . $agency_name . $valueSelected  . $search . $news_category_name);

    $pagetotal = wp_remote_retrieve_header($response, 'x-wp-totalpages');

    $response_code    = wp_remote_retrieve_response_code($response);
    $response_message = wp_remote_retrieve_response_message($response);

    if (200 != $response_code && ! empty($response_message)) {
        echo '<span class="nomore-btn" data-date="' . $valueSelected . '">';
        echo '<span class="c-pagination__main">No Results</span>';
        echo '</span>';
    } else {
        if ($nextPageToRetrieve == $pagetotal) {
            echo '<span class="nomore-btn" data-date="' . $valueSelected . '">';
            echo '<span class="c-pagination__main">No More Results</span>';
            echo '</span>';
        } elseif ($pagetotal <= 1) {
            echo '<button class="more-btn" data-page="' . $nextPageToRetrieve . '" data-date="' . $valueSelected . '">';
            echo '<span class="c-pagination__main">No More Results</span>';
            echo '<span class="c-pagination__count"> ' . $nextPageToRetrieve . ' of 1</span>';
            echo '</button>';
        } else {
            echo '<button class="more-btn" data-page="' . $nextPageToRetrieve . '" data-date="' . $valueSelected . '">';
            echo '<span class="c-pagination__main"><span class="u-icon u-icon--circle-down"></span> Load Next 10 Results</span>';
            echo '<span class="c-pagination__count"> ' . $nextPageToRetrieve . ' of ' . $pagetotal . '</span>';
            echo '</button>';
        }
    }

    die();
}
