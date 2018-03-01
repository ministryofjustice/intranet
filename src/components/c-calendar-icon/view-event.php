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
    $data = $event['start_date'];
  }
}

?>
<time class="c-calendar-icon" datetime="<?php echo $data;?>">
  <h2 class="u-visually-hidden">Date:</h2>
  <span class="c-calendar-icon--dow"><?php echo date("l", strtotime($data));?></span>
  <span class="c-calendar-icon--dom"><?php echo date("d M", strtotime($data));?></span>
  <span class="c-calendar-icon--my"><?php echo date("Y", strtotime($data));?></span>
</time>
