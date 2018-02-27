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
            '_iso_start_date',
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

function my_awesome_func( $data ) {
  $posts = get_posts( array(
    'author' => $data['id'],
  ) );
 
  if ( empty( $posts ) ) {
    return null;
  }
 
  return $posts[0]->post_title;
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'myplugin/v1', '/events/', array(
    'methods' => 'GET',
    'callback' => 'get_homepage_news_endpoint',
  ) );
} );