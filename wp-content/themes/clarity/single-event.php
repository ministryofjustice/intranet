<?php
use MOJ\Intranet\Agency;

/**
* The template for displaying all single event post
*
* @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
*
* @package WordPress
* @subpackage Clarity
* @since 1.0
* @version 1.0
*/

$oAgency        = new Agency();
$current_agency = $oAgency->getCurrentAgency() ?? array();
$region_id      = get_the_terms( get_the_id(), 'region' );
$terms          = get_the_terms( get_the_id(), 'agency' );

/**
* loop through and create array of all agencies associated with event
 *
* @return array of agency names
*/
if ( $terms ) {
	foreach ( $terms as $term ) {

		$term_array                        = (array) $term;
		$list_agency_names_used_by_event[] = $term_array['slug'];

	}
} else {
	// return an empty array so that var below recives var in array form
	$list_agency_names_used_by_event[] = array();
}

get_header();
?>

  <div id="maincontent" class="u-wrapper l-main t-events">
	
	<?php

	// display correct breadcrumb
	if ( $region_id ) :
		get_template_part( 'src/components/c-breadcrumbs/view', 'region-single' );
	else :
		get_template_part( 'src/components/c-breadcrumbs/view', 'event' );
	endif;

	/**
	* display either event or message notifying user they are viewing from
	* an agency that is not associated with event
	*/
	if ( in_array( $current_agency['shortcode'], $list_agency_names_used_by_event ) ) {
		get_template_part( 'src/components/c-event-article/view' );
	} else {

		echo '<div class="o-notification-panel">';

		if ( $terms ) {

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
		} else {
			echo 'Unable identify region and display event.';
		}

		echo '</div>';
	}
	?>

	<section class="l-full-page">

	<?php
	// bottom of event page last updated and share/like buttons
	get_template_part( 'src/components/c-last-updated/view' );
	get_template_part( 'src/components/c-share-post/view' );

	?>

	</section>

  </div>
<?php
get_footer();
