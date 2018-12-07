<?php
use MOJ\Intranet\Event;

/*
* Template Name: Events archive
*/

$oEvent = new Event();
$event  = $oEvent->get_event_list( 'search' );

get_header();
?>
  <div id="maincontent" class="u-wrapper l-main t-article-list">
	<h1 class="o-title o-title--page"><?php the_title(); ?></h1>

	<div class="l-secondary" role="complementary">
		<?php get_template_part( 'src/components/c-content-filter/view', 'events' ); ?>
	</div>

	<div class="l-primary" role="main">
		<?php
		if ( $event ) :
			echo '<h2 class="o-title o-title--section" id="title-section">Upcoming events</h2>';
			echo '<div id="content">';
			get_template_part( 'src/components/c-events-list/view' );
			echo '</div>';
		else :
			echo 'No events are currently listed :(';
		endif;
		?>
	</div>
  </div>


  <?php

  $groups = acf_get_local_field_groups();
  $json = [];

  foreach ($groups as $group) {
      // Fetch the fields for the given group key
      $fields = acf_get_local_fields($group['key']);

      // Remove unecessary key value pair with key "ID"
      unset($group['ID']);

      // Add the fields as an array to the group
      $group['fields'] = $fields;

      // Add this group to the main array
      $json[] = $group;
  }

  $json = json_encode($json, JSON_PRETTY_PRINT);
  // Optional - echo the JSON data to the page
  echo "<pre>";
  echo $json;
  echo "</pre>";

  // Write output to file for easy import into ACF.
  // The file must be writable by the server process. In this case, the file is located in
  // the current theme directory.
  $file = get_template_directory() . '/acf-import.json';
  file_put_contents($file, $json );

  ?>


<?php
get_footer();
