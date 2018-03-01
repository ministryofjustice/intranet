<?php
use MOJ\Intranet\Events;

if (!defined('ABSPATH')) {
    die();
}
/*
* Displays event calendar icon that sits on the event single page.
*
*/
$oEvents = new Events();

$eventsList = $oEvents->getEvents();
$post_id = get_the_ID();

foreach ($eventsList as $event) {
    if ($post_id == $event['id']) {
        $start_date = $event['start_date'];
        $end_date = $event['end_date'];

        if (isset($start_date) && isset($end_date)) {
            $start_end_date = date("d M", strtotime($start_date)) . ' - ' . date("d M", strtotime($end_date));
            $date = $event['start_date'];
            if ($start_date === $end_date) {
                $start_end_date = date("d M", strtotime($start_date));
                $date = $event['start_date'];
            }
        } else {
            $start_end_date = '';
            $date = '';
        }
    }
}

?>
<time class="c-calendar-icon" datetime="<?php echo $date;?>">
  <h2 class="u-visually-hidden">Date:</h2>
  <span class="c-calendar-icon--dow"><?php echo date("l", strtotime($date));?></span>
  <span class="c-calendar-icon--dom"><?php echo $start_end_date;?></span>
  <span class="c-calendar-icon--my"><?php echo date("Y", strtotime($date));?></span>
</time>
