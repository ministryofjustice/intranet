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