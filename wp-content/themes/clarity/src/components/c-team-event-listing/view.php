<?php
use MOJ\Intranet\Teams;

/*
*
* This page is for displaying the event item when it appears in a list format.
* Currently on homepage, team events and event archive.
*
*/

// Number of events displayed
$event_count = 4;

$oTeam = new Teams();
$event = $oTeam->team_events_api( $event_count );

if ( isset( $event ) ) :

	echo '<h1 class="o-title o-title--section">Events</h1>';

	foreach ( $event as $key => $post ) :

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

		if ( $all_day === true ) {
			$all_day = 'all_day';
		}
		?>
<!-- c-team-event-listing starts here -->
<section class="c-team-event-listing">

		<?php
		// If start date and end date seleced are the same, just display first date.
		if ( $start_date === $end_date ) {
			   $multidate = date( 'd M', strtotime( $start_date ) );
		} else {
			 $multidate = date( 'd M', strtotime( $start_date ) ) . ' - ' . date( 'd M', strtotime( $end_date ) );
		}
		?>

  <h1><a href="<?php echo $post_url; ?>"><?php echo $event_title['rendered']; ?></a></h1>

  <div class="c-event-listing--date" datetime="<?php echo $start_date; ?>">
	<h2>Date:</h2><?php echo $day . ' ' . $multidate . ' ' . $year; ?>
  </div>

  <article class="c-events-item-byline__team">

	<header>
		<?php
		if ( empty( $all_day ) ) {

			if ( isset( $start_time ) || isset( $end_time ) ) {
				$time = $start_time . ' - ' . $end_time;
			} else {
				$time = '';
			}
		} else {
			$time = 'All day';
		}
		?>

	  <div class="c-event-listing--time">
		<h2>Time:</h2><?php echo $time; ?>
	  </div>

		<?php if ( isset( $location ) ) : ?>

		<div class="c-event-listing--location">
		  <h2>Location:</h2><address><?php echo $location; ?></address>
		</div>

		<?php endif; ?>
	</header>
  </article>

</section>
<!-- c-team-event-listing ends here -->

		<?php
endforeach; // ($event as $key => $post):
endif; // is_array($event)

wp_reset_query();
