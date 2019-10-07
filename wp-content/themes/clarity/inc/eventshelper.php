<?php
namespace MOJ\Intranet;

if (!defined('ABSPATH')) die();

class EventsHelper  {

    public function get_event($event_id) {
        $args = array(
            'post_type'      => 'event',
            'posts_per_page' => 1,
            'p' => $event_id,
        );

        $event = get_posts( $args );

        if(is_array($event) && count($event) == 1){
            $event[0]->event_start_date = get_post_meta( $event[0]->ID, '_event-start-date', true );
            $event[0]->event_end_date   = get_post_meta( $event[0]->ID, '_event-end-date', true );
            $event[0]->event_start_time = get_post_meta( $event[0]->ID, '_event-start-time', true );
            $event[0]->event_end_time   = get_post_meta( $event[0]->ID, '_event-end-time', true );
            $event[0]->event_location   = get_post_meta( $event[0]->ID, '_event-location', true );
            $event[0]->event_allday     = get_post_meta( $event[0]->ID, '_event-allday', true );

            $event[0]->agency   = wp_get_post_terms( $event[0]->ID, 'agency' );
            $event[0]->region   = wp_get_post_terms( $event[0]->ID, 'region', true );
            $event[0]->campaign = wp_get_post_terms( $event[0]->ID, 'campaign_category', true );

            $event[0]->url = get_post_permalink( $event[0]->ID );

            return $event[0];

        }
        else {
            return false;
        }
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

}
