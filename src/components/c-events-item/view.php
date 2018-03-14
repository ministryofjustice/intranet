<?php
use MOJ\Intranet\Event;

if (!defined('ABSPATH')) {
    die();
}

/*
*
* This page is for displaying single event item make up of the calendar and event byline.
* Normally used on the single event page.
*
*/
$oEvent = new Event();
$event = $oEvent->get_event_list('search');
$post_id = get_the_id();
?>
<!-- c-events-item starts here -->
<section class="c-events-item">
    <?php

      foreach ($event as $key => $post) {
          $event_id = $post['ID'];
          if ($post_id == $event_id) {
              $start_time = $post['event_start_time'];
              $end_time = $post['event_end_time'];
              $start_date = $post['event_start_date'];
              $end_date = $post['event_end_date'];
              $location = $post['event_location'];
              $date = $post['event_start_date'];
              $day = date("l", strtotime($start_date));
              $year = date("Y", strtotime($start_date));
              $all_day = get_post_meta($post_id, '_event-allday', true);

              if ($all_day == true) {
                  $all_day = 'all_day';
              }
          }
      }

      // using include() instead of get_template_part to pass variables to components
      include(locate_template('src/components/c-calendar-icon/view.php'));
      include(locate_template('src/components/c-events-item-byline/view.php'));
    ?>
</section>
<!-- c-events-item ends here -->
