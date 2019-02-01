<?php
use MOJ\Intranet\Teams;

/*
*
* This page is for displaying the event item when it appears in a list format.
* Currently on homepage, team events and event archive.
*
*/
$post_id = get_the_ID();

// Number of events displayed. This needs to be refactored as part of a wider event api refactor so it is not capped at 100.
$event_count = 100;

$oTeam = new Teams();
$event = $oTeam->team_events_api( $event_count );

if ( is_array( $event ) ) :

	foreach ( $event as $key => $post ) :

		if ( $post['id'] === $post_id ) :
			$event_id    = $post['id'];
			$post_url    = $post['link'];
			$event_title = $post['title'];
			$start_time  = (string) get_post_meta( $event_id, '_event-start-time', true );
			$end_time    = (string) get_post_meta( $event_id, '_event-end-time', true );
			$start_date  = get_post_meta( $event_id, '_event-start-date', true );
			$end_date    = get_post_meta( $event_id, '_event-end-date', true );
			$location    = (string) get_post_meta( $event_id, '_event-location', true );
			$date        = get_post_meta( $event_id, '_event-start-date', true );
			$year        = date( 'Y', strtotime( $start_date ) );
			$month       = date( 'M', strtotime( $start_date ) );
			$day         = date( 'l', strtotime( $start_date ) );
			$all_day     = get_post_meta( $event_id, '_event-allday', true );

			if ( $all_day === true ) :
				$all_day = 'all_day';
		  endif;

			echo '<div class="c-events-item-team-homepage">';
			include locate_template( 'src/components/c-calendar-icon/view.php' );
			include locate_template( 'src/components/c-events-item-byline/view.php' );
			echo '</div>';
	  endif;
  endforeach;
endif;
