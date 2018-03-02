<?php

//Show meta fields on EVENT
//add_action( 'rest_api_init', 'api_register_events_meta' );

// function api_register_events_meta()
// {

//     $allowed_meta_fields = array(
//         'event' => array (
//             '_event-start-date',
//             '_event-end-date',
//             '_event-start-time',
//             '_event-end-time',
//             '_event-location',
//             '_event-allday',
//         )
//     );

//     foreach ($allowed_meta_fields as $posttype => $metas)
//     {
//         foreach ($metas as $meta_field)
//         {
//             register_rest_field( $posttype,
//                 $meta_field,
//                 array(
//                     'get_callback'    => 'api_get_event_meta_value',
//                     'update_callback' => null,
//                     'schema'          => null,
//                 )
//             );
//         }
//     }
// }

// function api_get_event_meta_value( $object, $field_name, $request ) {
//     return get_post_meta( $object[ 'id' ], $field_name, true );
// }

add_action('rest_api_init', function () {

    // register_rest_route('intranet/v2/', 'future-events/(?P<page>[1-9]{1,2})', 
    //     array (
    //         'methods'             => 'GET',
    //         'callback'            => 'add_custom_events_endpoint',
    //         'args' => array(
    //             'page' => array (
    //                 'required' => true
    //             ),  
    //         ),
    //         'permission_callback' => function (WP_REST_Request $request) 
    //         {return true;}
    //     )
    // );
    register_rest_route('intranet/v2/', 'future-events/(?P<agency>[a-zA-Z0-9-]+)/', 
        array (
            'methods'             => 'GET',
            'callback'            => 'add_custom_events_endpoint',
            'args' => array(
     
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

    $page = $request['page'];

    //Taxonomy queries: Agency and Regions
    $agency = sanitize_text_field($request->get_param( 'agency' ));

    $options['tax_query'] = array (
        'relation' => 'AND'
    );
    $options['tax_query'][0] = [
        'taxonomy' => 'agency',
        'field' => 'slug',
        'tag' => $agency,
    ];

    $args = array (
        'orderby' => $options['search_orderby'],
        'meta_query' => $options['meta_query'],
        'post_type' => 'event',
        //'posts_per_page' => 2,
        'offset' => $page,
        'tax_query' => $options['tax_query']
    );

    //print_r(new WP_Query());

    $events = get_posts($args);

    $i = 0;

    //print_r($events);
    foreach ($events as $event) {
    
        $events[$i]->event_start_date = get_post_meta($event->ID, '_event-start-date', true);
        $events[$i]->event_end_date = get_post_meta($event->ID, '_event-end-date', true);
        $events[$i]->event_start_time = get_post_meta($event->ID, '_event-start-time', true);
        $events[$i]->event_end_time = get_post_meta($event->ID, '_event-end-time', true);
        $events[$i]->event_location = get_post_meta($event->ID, '_event-location', true);
        
        $events[$i]->agency = wp_get_post_terms($event->ID, 'agency');
        $events[$i]->region = wp_get_post_terms($event->ID, 'region', true);
        $events[$i]->campaign = wp_get_post_terms($event->ID, 'campaign_category', true);

        $i ++;
    }

    if ( empty( $events ) ) {
        return null;
    }

    return $events;

}