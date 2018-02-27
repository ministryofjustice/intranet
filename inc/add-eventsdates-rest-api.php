<?php

//Show meta fields on EVENT
add_action( 'rest_api_init', 'api_register_events_meta' );

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

    register_rest_route('vinh/v2', '/future-events', array (
        'methods'             => 'GET',
        'callback'            => 'get_test_endpoint',
        'permission_callback' => function (WP_REST_Request $request) {
            return true;
        }
    ));
});

function get_test_endpoint(){
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

    $args = array (
        'orderby' => $options['search_orderby'],
        'meta_query' => $options['meta_query'],
        'post_type' => ['event'],
    );
    $events = get_posts($args);

    $i = 0;

    //print_r($events);
    foreach ($events as $event) {

        $events[$i]->event_start_date = get_post_meta($event->ID, '_event-start-date', true);
        $events[$i]->event_end_date = get_post_meta($event->ID, '_event-end-date', true);
        $events[$i]->event_start_time = get_post_meta($event->ID, '_event-start-time', true);
        $events[$i]->event_end_time = get_post_meta($event->ID, '_event-end-time', true);
        $events[$i]->event_location = get_post_meta($event->ID, '_event-location', true);

        $i ++;
    }

    if ( empty( $events ) ) {
        return null;
    }

    return $events;
}