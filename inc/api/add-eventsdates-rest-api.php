<?php

// Registering custom endpoints
add_action('rest_api_init', function () {
    // Events by agency
    register_rest_route('intranet/v2/', 'future-events/(?P<agency>[a-zA-Z0-9-]+)/',
        array (
            'methods'             => 'GET',
            'callback'            => 'add_custom_events_endpoint',
            'permission_callback' => function (WP_REST_Request $request)
            {return true;}
        )
    );
    // Events by search (keywords)
    register_rest_route('intranet/v2/', 'future-events/(?P<agency>[a-zA-Z0-9-]+)/(?P<search>[a-z0-9]+(?:-[a-z0-9]+)*)/',
        array (
            'methods'             => 'GET',
            'callback'            => 'add_custom_events_endpoint',
            'args' => array(
                'search' => array (
                    'required' => true
                ),
                'agency' => array (
                    'required' => true
                ),
            ),
            'permission_callback' => function (WP_REST_Request $request)
            {return true;}
        )
    );
    // Events by regions
    register_rest_route('intranet/v2/', 'region-events/(?P<agency>[a-zA-Z0-9-]+)/(?P<region>[a-z0-9]+(?:-[a-z0-9]+)*)/',
        array (
            'methods'             => 'GET',
            'callback'            => 'add_region_events_endpoint',
            'args' => array(
                'agency' => array (
                    'required' => true
                ),
                'region' => array (
                    'required' => true
                ),
            ),
            'permission_callback' => function (WP_REST_Request $request)
            {return true;}
        )
    );
    // Events by campaigns
    register_rest_route('intranet/v2/', 'campaign-events/(?P<agency>[a-zA-Z0-9-]+)/(?P<campaign>[a-z0-9]+(?:-[a-z0-9]+)*)/',
        array (
            'methods'             => 'GET',
            'callback'            => 'add_campaign_events_endpoint',
            'args' => array(
                'agency' => array (
                    'required' => true
                ),
                'campaign' => array (
                    'required' => true
                ),
            ),
            'permission_callback' => function (WP_REST_Request $request)
            {return true;}
        )
    );
});

function add_campaign_events_endpoint(WP_REST_Request $request){
    //Order By
    $options['search_orderby'] = array(
        '_event-start-date' => 'ASC',
        '_event-end-date' => 'ASC',
        'title' => 'ASC'
    );

    //Get events that are for today onwards
    $options ['meta_query'] = array(
        array
        (
            'relation' => 'OR',
                array (
                'key' => '_event-start-date',
                'value' => date('Y-m-d'),
                'type' => 'date',
                'compare' => '>='
                ),
                array (
                'key' => '_event-end-date',
                'value' => date('Y-m-d'),
                'type' => 'date',
                'compare' => '>='
                ),
        )
    );

    // request the query given in the url
    $campaign = $request['campaign'];
    $agency = $request['agency'];

    $args = array (
        'orderby' => $options['search_orderby'],
        'meta_query' => $options['meta_query'],
        'post_type' => 'event',
        'posts_per_page' => -1,
        'nopaging' => true,
        'tax_query' => array(
            array(
                'taxonomy' => 'agency',
                'field' => 'term_id',
                'terms' => $agency,
            ),
            array(
                'taxonomy' => 'campaign_category',
                'field' => 'term_id',
                'terms' => $campaign,
            ),
        )
    );


    $events = get_posts($args);

    $i = 0;

    foreach ($events as $event) {

        $events[$i]->event_start_date = get_post_meta($event->ID, '_event-start-date', true);
        $events[$i]->event_end_date = get_post_meta($event->ID, '_event-end-date', true);
        $events[$i]->event_start_time = get_post_meta($event->ID, '_event-start-time', true);
        $events[$i]->event_end_time = get_post_meta($event->ID, '_event-end-time', true);
        $events[$i]->event_location = get_post_meta($event->ID, '_event-location', true);
        $events[$i]->event_allday = get_post_meta($event->ID, '_event-allday', true);

        $events[$i]->agency = wp_get_post_terms($event->ID, 'agency');
        $events[$i]->region = wp_get_post_terms($event->ID, 'region', true);
        $events[$i]->campaign = wp_get_post_terms($event->ID, 'campaign_category', true);

        $events[$i]->url = get_post_permalink($event->ID);

        $i ++;
    }

    if ( empty( $events ) ) {
        return null;
    }

    // count number of posts
    $total_post_count = count( $events );

    // display url parameters on json api.
    $url_query = array(
        'agency' => $agency,
        'campaign' => $campaign,
        'post_count' => $total_post_count,
    );

    return $arrayJson = array(
        'url' => $url_query,
        'events' => $events,
    );
}

function add_region_events_endpoint(WP_REST_Request $request){
    //Order By
    $options['search_orderby'] = array(
        '_event-start-date' => 'ASC',
        '_event-end-date' => 'ASC',
        'title' => 'ASC'
    );

    //Get events that are for today onwards
    $options ['meta_query'] = array(
        array
        (
            'relation' => 'OR',
                array (
                'key' => '_event-start-date',
                'value' => date('Y-m-d'),
                'type' => 'date',
                'compare' => '>='
                ),
                array (
                'key' => '_event-end-date',
                'value' => date('Y-m-d'),
                'type' => 'date',
                'compare' => '>='
                ),
        )
    );

    // request the query given in the url
    $region = $request['region'];
    $agency = $request['agency'];

    $args = array (
        'orderby' => $options['search_orderby'],
        'meta_query' => $options['meta_query'],
        'post_type' => 'event',
        'posts_per_page' => -1,
        'nopaging' => true,
        'tax_query' => array(
            array(
                'taxonomy' => 'agency',
                'field' => 'term_id',
                'terms' => $agency,
            ),
            array(
                'taxonomy' => 'region',
                'field' => 'term_id',
                'terms' => $region,
            ),
        )
    );

    $events = get_posts($args);

    $i = 0;


    foreach ($events as $event) {

        $events[$i]->event_start_date = get_post_meta($event->ID, '_event-start-date', true);
        $events[$i]->event_end_date = get_post_meta($event->ID, '_event-end-date', true);
        $events[$i]->event_start_time = get_post_meta($event->ID, '_event-start-time', true);
        $events[$i]->event_end_time = get_post_meta($event->ID, '_event-end-time', true);
        $events[$i]->event_location = get_post_meta($event->ID, '_event-location', true);
        $events[$i]->event_allday = get_post_meta($event->ID, '_event-allday', true);

        $events[$i]->agency = wp_get_post_terms($event->ID, 'agency');
        $events[$i]->region = wp_get_post_terms($event->ID, 'region', true);
        $events[$i]->campaign = wp_get_post_terms($event->ID, 'campaign_category', true);

        $events[$i]->url = get_post_permalink($event->ID);

        $i ++;
    }

    if ( empty( $events ) ) {
        return null;
    }

    // count number of posts
    $total_post_count = count( $events );

    // display url parameters on json api.
    $url_query = array(
        'agency' => $agency,
        'region' => $region,
        'post_count' => $total_post_count,
    );

    return $arrayJson = array(
        'url' => $url_query,
        'events' => $events,
    );
}

function add_custom_events_endpoint(WP_REST_Request $request){
    //Order By
    $options['search_orderby'] = array(
        '_event-start-date' => 'ASC',
        '_event-end-date' => 'ASC',
        'title' => 'ASC'
    );

    //Get events that are for today onwards
    $options ['meta_query'] = array(
        array
        (
            'relation' => 'OR',
                array (
                'key' => '_event-start-date',
                'value' => date('Y-m-d'),
                'type' => 'date',
                'compare' => '>='
                ),
                array (
                'key' => '_event-end-date',
                'value' => date('Y-m-d'),
                'type' => 'date',
                'compare' => '>='
                ),
        )
    );

    // request the query given in the url
    $search = $request['search'];
    $agency = $request['agency'];

    $args = array (
        'orderby' => $options['search_orderby'],
        'meta_query' => $options['meta_query'],
        'post_type' => 'event',
        's' => $search,
        'posts_per_page' => -1,
        'nopaging' => true,
        'tax_query' => array(
            array(
                'taxonomy' => 'agency',
                'field' => 'term_id',
                'terms' => $agency,
            )
        )
    );

    $events = get_posts($args);

    $i = 0;


    foreach ($events as $event) {

        $events[$i]->event_start_date = get_post_meta($event->ID, '_event-start-date', true);
        $events[$i]->event_end_date = get_post_meta($event->ID, '_event-end-date', true);
        $events[$i]->event_start_time = get_post_meta($event->ID, '_event-start-time', true);
        $events[$i]->event_end_time = get_post_meta($event->ID, '_event-end-time', true);
        $events[$i]->event_location = get_post_meta($event->ID, '_event-location', true);
        $events[$i]->event_allday = get_post_meta($event->ID, '_event-allday', true);

        $events[$i]->agency = wp_get_post_terms($event->ID, 'agency');
        $events[$i]->region = wp_get_post_terms($event->ID, 'region', true);
        $events[$i]->campaign = wp_get_post_terms($event->ID, 'campaign_category', true);

        $events[$i]->url = get_post_permalink($event->ID);

        $i ++;
    }

    if ( empty( $events ) ) {
        return null;
    }

    // count number of posts
    $total_post_count = count( $events );

    // display url parameters on json api.
    $url_query = array(
        'agency' => $agency,
        'search' => $search,
        'post_count' => $total_post_count,
    );

    return $arrayJson = array(
        'url' => $url_query,
        'events' => $events,
    );


}
