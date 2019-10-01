<?php
use MOJ\Intranet\Agency;
/*
* Template Name: Events archive
*/
get_header();
?>
  <div id="maincontent" class="u-wrapper l-main t-article-list">
	<h1 class="o-title o-title--page"><?php the_title(); ?></h1>

	<div class="l-secondary" role="complementary">
		<?php get_template_part( 'src/components/c-content-filter/view', 'events' ); ?>
	</div>

	<div class="l-primary" role="main">
		<?php

        /*
         *
         *   'orderby'   => 'meta_value_num',
            'meta_key'  => '_event-start-time',
            'order'     => 'ASC',
         */


        // Get events that are for today onwards
        $options ['meta_query'] = array(
            array(
                'relation' => 'OR',
                array(
                    'key' => '_event-start-date',
                    'value' => date('Y-m-d'),
                    'type' => 'date',
                    'compare' => '>=',
                ),
                array(
                    'key' => '_event-end-date',
                    'value' => date('Y-m-d'),
                    'type' => 'date',
                    'compare' => '>=',
                ),

            ),
            array(
                'start_time_clause' => array(
                    'key' =>  '_event-start-time',
                    'compare' => 'EXISTS',
                ),
            )
        );

        $args = array(
            'orderby'   => array ('_event-start-date' => 'ASC','start_time_clause' => 'ASC'),
            'post_type'      => 'event',
            'posts_per_page' => 5,
            'nopaging'       => true,
            'meta_query'     => $options['meta_query'],
        );

        $events = get_posts( $args );

        foreach ( $events as $event ) {

            echo "Date ";
            echo get_post_meta( $event->ID, '_event-start-date', true );
            echo " Time ";
            echo get_post_meta( $event->ID, '_event-start-time', true );
            echo "<br/>";



        }


        $oAgency = new Agency();
        $activeAgency = $oAgency->getCurrentAgency();

        $agency_term_id = $activeAgency['wp_tag_id'];

        $events = get_events($agency_term_id);

		if ( $events ) :
			echo '<h2 class="o-title o-title--section" id="title-section">Upcoming events</h2>';
			echo '<div id="content">';
            include locate_template( 'src/components/c-events-list/view.php' );
			echo '</div>';
		else :
			echo 'No events are currently listed :(';
		endif;
		?>
	</div>
  </div>

<?php
get_footer();
