<?php

/*
*
* Registering new ACF fields for exsiting API routes
* https://developer.wordpress.org/reference/functions/register_rest_field/
*
*/
add_action( 'rest_api_init', 'add_date_meta_team_events_api' );

function add_date_meta_team_events_api() {
	$acf_event_fields = [
		'event-start-date',
		'event-end-date',
		'event-start-time',
		'event-end-time',
		'event-location',
		'event-allday',
	];

	foreach ( $acf_event_fields as $field_value ) {
		register_rest_field(
			'team_events',
			$field_value, // name-of-field-to-return
			[
				'get_callback'    => 'team_event_date_endpoint',
				'update_callback' => null,
				'schema'          => null,
			]
		);
	}
}

/*
*
* Team event endpoint
$ object is the team_events API
*
*/
function team_event_date_endpoint( $object, $field_value ) {
	$acf_value_key_pair = get_post_meta( $object['id'], '_' . $field_value, true );

	return $acf_value_key_pair;
}
