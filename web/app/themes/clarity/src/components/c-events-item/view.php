<?php
use MOJ\Intranet\EventsHelper;

if (! defined('ABSPATH')) {
    die();
}

/*
*
* This page is for displaying single event item make up of the calendar and event byline.
* Normally used on the single event page.
*
*/

$EventsHelper  = new EventsHelper();
$event = $EventsHelper->get_event(get_the_id());


?>
<!-- c-events-item starts here -->
<section class="c-events-item">
    <?php
    if (is_object($event)) {
        $start_date = $event->event_start_date;
        $end_date   = $event->event_end_date;
        $start_time = $event->event_start_time;
        $end_time   = $event->event_end_time;
        $location   = $event->event_location;
        $date       = $event->event_start_date;
        $day        = date('l', strtotime($start_date));
        $month      = date('M', strtotime($start_date));
        $year       = date('Y', strtotime($start_date));
        $all_day    = $event->event_allday;

        if ($all_day == true) {
            $all_day = 'all_day';
        }
    }

      // using include() instead of get_template_part to pass variables to components
      require locate_template('src/components/c-calendar-icon/view.php');
      require locate_template('src/components/c-events-item-byline/view.php');
    ?>
</section>
<!-- c-events-item ends here -->
