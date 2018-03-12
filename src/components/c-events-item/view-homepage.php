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
$event = $oEvent->get_event_list('search');

$event = array_splice($event, 0, 2);
$post_id = get_the_id();
?>
<!-- c-events-item starts here -->
<?php
  foreach ($event as $key => $post) {
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
      $all_day = get_post_meta($post_id, '_event-allday', true);

      if ($all_day == true) {
          $all_day = 'all_day';
      }

      echo '<section class="c-events-item-homepage">';
      include(locate_template('src/components/c-calendar-icon/view.php'));
      include(locate_template('src/components/c-events-item-byline/view.php'));
      echo '</section>';
  }
?>

<!-- c-events-item ends here -->
