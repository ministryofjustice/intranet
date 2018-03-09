<?php
use MOJ\Intranet\Event;

if (!defined('ABSPATH')) {
    die();
}

/*
*
* This page is for displaying the event item, both the calendar and event byline that appear in the sidebar.
*
*/
$oEvent = new Event();
$event = $oEvent->getEventItem($post);

  /*
  *
  * Properties required to populate the calendar icon and byline.
  *
  */
  $start_time = $event['start_time'];
  $end_time = $event['end_time'];
  $start_date = $event['start_date'];
  $end_date = $event['end_date'];
  $all_day = $event['all_day'];
  $location = $event['location'];
  $date = $event['start_date'];
  $day = date("l", strtotime($date));
  $year = date("Y", strtotime($date));

  // If the user selects start date and end date that are the same, just display one date.
  if ($start_date === $end_date) {
      $date = date("d M", strtotime($start_date));
  } else {
      $date = date("d M", strtotime($start_date)) . ' - ' . date("d M", strtotime($end_date));
  }

?>
<!-- c-events-item-aside starts here -->
<section class="c-events-item-aside">
    <?php
    // include() rather then get_template_part() so that above variables are passed.
    include(locate_template('src/components/c-calendar-icon/view-post.php'));
    include(locate_template('src/components/c-events-item-byline/view.php'));
    ?>
</section>
<!-- c-events-item-aside ends here -->
