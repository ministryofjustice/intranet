<?php

//Show meta fields on EVENT
//add_action( 'rest_api_init', 'api_register_events_meta' );

function api_register_events_meta()
{

    $allowed_meta_fields = array(
        'event' => array (
            '_event-start-date',
            '_event-end-date',
            '_event-start-time',
            '_event-end-time',
            '_event-location',
            '_event-allday',
        )
    );

    foreach ($allowed_meta_fields as $posttype => $metas)
    {
        foreach ($metas as $meta_field)
        {
            register_rest_field( $posttype,
                $meta_field,
                array(
                    'get_callback'    => 'api_get_event_meta_value',
                    'update_callback' => null,
                    'schema'          => null,
                )
            );
        }
    }
}

function api_get_event_meta_value( $object, $field_name, $request ) {
    return get_post_meta( $object[ 'id' ], $field_name, true );
}

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
});

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
                'field' => 'slug',
                'terms' => $agency,
            )
        )
    );

    //print_r(new WP_Query());

    $events = get_posts($args);

    $i = 0;

    //print_r($events);
    foreach ($events as $event) {
        //print_r(get_post_meta($event->ID, 'slug'));
        $events[$i]->event_start_date = get_post_meta($event->ID, '_event-start-date', true);
        $events[$i]->event_end_date = get_post_meta($event->ID, '_event-end-date', true);
        $events[$i]->event_start_time = get_post_meta($event->ID, '_event-start-time', true);
        $events[$i]->event_end_time = get_post_meta($event->ID, '_event-end-time', true);
        $events[$i]->event_location = get_post_meta($event->ID, '_event-location', true);
        
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

    return $arrayName = array(
        'url' => $url_query,
        'events' => $events,
    );
    //return $events;


}