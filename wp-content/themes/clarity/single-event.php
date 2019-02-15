<?php
use MOJ\Intranet\Agency;
use MOJ\Intranet\Event;
/*
* Single event post
*/

$oEvent  = new Event();
$oAgency = new Agency();

$post_id   = get_the_id();
$region_id = get_the_terms( $post_id, 'region' );
$terms     = get_the_terms( $post_id, 'agency' );
$post_type = get_post_type( $post_id );
$event     = $oEvent->get_event_list( 'search' );

get_header();

?>
  <div id="maincontent" class="u-wrapper l-main t-events">
	<?php

	// display correct breadcrumb
	if ( $region_id ) {
		get_template_part( 'src/components/c-breadcrumbs/view', 'region-single' );
	} else {
		get_template_part( 'src/components/c-breadcrumbs/view', 'event' );
	}

	// display correct page based on agency access
	if ( $event ) {
		get_template_part( 'src/components/c-event-article/view' );
	} else {

		echo '<div class="o-notification-panel">';

		if ( is_array( $terms ) ) {

			$agency_count = sizeof( $terms );

			if ( $agency_count > 1 ) {
				$subject_verb   = 'one of';
				$subject_verb_2 = 's';
			} else {
				$subject_verb   = '';
				$subject_verb_2 = '';
			}

			echo '<h1>';
			echo 'The event you are trying to access is not associated with your current agency ';
			echo '</h1>';
			echo 'View the event by switching agencies. Use ' . $subject_verb . ' the below link' . $subject_verb_2 . ':';
			echo '<br><br>';

			foreach ( $terms as $term ) {

				$agency_name = $term->name;
				$agency_slug = $term->slug;

				echo ' <a href="' . get_permalink() . '?agency=' . esc_attr( $agency_slug ) . '">' . esc_attr( $agency_name ) . '</a>,';
			}
		}

		echo '</div>';
	}
	?>

	<section class="l-full-page">
	<?php
	get_template_part( 'src/components/c-last-updated/view' );
	get_template_part( 'src/components/c-share-post/view' );
	?>
	</section>

  </div>
<?php
get_footer();
