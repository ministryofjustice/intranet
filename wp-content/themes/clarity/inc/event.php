<?php


if ( ! defined( 'ABSPATH' ) ) {
	die();
}

function get_events($agency, $future = true, $search = ''){
    // Order By
    $options['search_orderby'] = array(
        '_event-start-date'   => 'ASC',
        'start_time_clause'   => 'ASC',
    );


    if($future == true) {

        // Get events that are for today onwards
        $options ['meta_query'] = array(
            array(
                'relation' => 'OR',
                array(
                    'key' => '_event-start-date',
                    'value' => date('Y-m-d'),
                    'type' => 'date',
                    'compare' => '>=',
                ),
                array(
                    'key' => '_event-end-date',
                    'value' => date('Y-m-d'),
                    'type' => 'date',
                    'compare' => '>=',
                ),
            ),
            array(
                'start_time_clause' => array(
                    'key' =>  '_event-start-time',
                    'compare' => 'EXISTS',
                ),
            )
        );
    }

    $args = array(
        'orderby'        => $options['search_orderby'],
        'meta_query'     => $options['meta_query'],
        'post_type'      => 'event',
        's'              => $search,
        'posts_per_page' => -1,
        'nopaging'       => true,
        'tax_query'      => array(
            array(
                'taxonomy' => 'agency',
                'field'    => 'term_id',
                'terms'    => $agency,
            ),
        ),
    );

    $events = get_posts( $args );

    $i = 0;

    foreach ( $events as $event ) {
        $events[ $i ]->event_start_date = get_post_meta( $event->ID, '_event-start-date', true );
        $events[ $i ]->event_end_date   = get_post_meta( $event->ID, '_event-end-date', true );
        $events[ $i ]->event_start_time = get_post_meta( $event->ID, '_event-start-time', true );
        $events[ $i ]->event_end_time   = get_post_meta( $event->ID, '_event-end-time', true );
        $events[ $i ]->event_location   = get_post_meta( $event->ID, '_event-location', true );
        $events[ $i ]->event_allday     = get_post_meta( $event->ID, '_event-allday', true );

        $events[ $i ]->agency   = wp_get_post_terms( $event->ID, 'agency' );
        $events[ $i ]->region   = wp_get_post_terms( $event->ID, 'region', true );
        $events[ $i ]->campaign = wp_get_post_terms( $event->ID, 'campaign_category', true );

        $events[ $i ]->url = get_post_permalink( $event->ID );

        $i ++;
    }

    if ( empty( $events ) ) {
        return null;
    }
    else {
        return $events;
    }


}
