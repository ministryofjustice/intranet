<?php
use MOJ\Intranet\Event;

/*
*
* This page is for displaying the event item when it appears in a list format.
* Currently on homepage, team events and event archive.
*
*/

$oEvent = new Event();
$event = $oEvent->get_event_list('search');

if ( isset( $event ) ) :

	// Limit events listed on page to one for homepage display
	if (is_front_page()) {
			$event = array_splice($event, 0, 1);
	}

	foreach ($event as $key => $post) :
			$event_id = $post['ID'];
			$post_url = $post["url"];
			$event_title = $post["post_title"];
			$start_time = $post['event_start_time'];
			$end_time = $post['event_end_time'];
			$start_date = $post['event_start_date'];
			$end_date = $post['event_end_date'];
			$location = $post['event_location'];
			$date = $post['event_start_date'];
			$year = date('Y', strtotime($start_date));
			$month = date('M', strtotime($start_date));
			$day = date('l', strtotime($start_date));
			$all_day = get_post_meta($event_id, '_event-allday', true);

			if ($all_day === true) {
					$all_day = 'all_day';
			}

		?>

<!-- c-event-listing starts here -->

<h1 class="o-title o-title--subtitle">Next event</h1>

<section class="c-event-listing">

		<?php
		// If start date and end date seleced are the same, just display first date.
		if ( $start_date === $end_date ) {
			   $multidate = date( 'd M', strtotime( $start_date ) );
		} else {
			 $multidate = date( 'd M', strtotime( $start_date ) ) . ' - ' . date( 'd M', strtotime( $end_date ) );
		}
		?>

  <h1><a class="c-event-listing--title" href="<?php echo $post_url; ?>"><?php echo $event_title; ?></a></h1>

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
<!-- c-event-listing ends here -->

		<?php
endforeach; // ($event as $key => $post):
endif; // is_array($event)
