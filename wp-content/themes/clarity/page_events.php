<?php
use MOJ\Intranet\Event;

/*
* Template Name: Events archive
*/

$oEvent = new Event();
$event = $oEvent->get_event_list('search');

get_header();
?>
  <div id="maincontent" class="u-wrapper l-main t-article-list">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

    <div class="l-secondary" role="complementary">
      <?php get_template_part('src/components/c-content-filter/view', 'events'); ?>
    </div>

    <div class="l-primary" role="main">
      <?php
        if ($event):
          echo '<h2 class="o-title o-title--section" id="title-section">Upcoming events</h2>';
          echo '<div id="content">';
          get_template_part('src/components/c-events-list/view');
          echo '</div>';
        else:
          echo 'No events are currently listed :(';
        endif;
    ?>
    </div>
  </div>

<?php
get_footer();
