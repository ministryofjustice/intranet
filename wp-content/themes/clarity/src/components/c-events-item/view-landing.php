<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( is_array( $events ) ) {

    echo '<div class="data-type" data-type="event"></div>';
    echo '<h2 class="o-title o-title--section" id="title-section">Events</h2>';
	foreach ( $events as $key => $post ) {

		$event_id    = $post->ID;
		$post_url    = $post->url;
		$event_title = $post->post_title;
		$start_time  = $post->event_start_time;
		$end_time    = $post->event_end_time;
		$start_date  = $post->event_start_date;
		$end_date    = $post->event_end_date;
		$location    = $post->event_location;
		$date        = $post->event_start_date;
        $multiday         = $start_date != $end_date;
		$year        = date( 'Y', strtotime( $start_date ) );
		$month       = date( 'M', strtotime( $start_date ) );
		$day         = date( 'l', strtotime( $start_date ) );
		$all_day     = $post->event_allday;

		if ( $all_day === true ) {
			$all_day = 'all_day';
		}

		?>

		<article class="c-events-item" data-type="event">
				<?php include locate_template( 'src/components/c-calendar-icon/view.php' ); ?>

	        <h1><a href="<?php echo $post_url; ?>"><?php echo $event_title; ?></a></h1>

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

            <div class="c-events-item__time">
                <h2>Time:</h2>
                <?php echo $time; ?>
            </div>

				<?php
				if ( isset( $location ) ) :
					?>
		  <div class="c-events-item__location">
		  <h2>Location:</h2>
		  <address><?php echo $location; ?></address>
		  </div>
					<?php
		endif;
				?>

	  <span class="ie-clear"></span>
	  </article>

    <?php
	}
}
