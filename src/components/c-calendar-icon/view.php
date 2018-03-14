<?php

if (!defined('ABSPATH')) {
    die();
}

// If start date and end date seleced are the same, just display first date.
if ($start_date === $end_date) {
    $multidate = date("d M", strtotime($start_date));
} else {
    $multidate = date("d M", strtotime($start_date)) . ' - ' . date("d M", strtotime($end_date));
}
?>
<!-- c-calendar-icon starts here -->
<time class="c-calendar-icon" datetime="<?php echo $start_date;?>">
  <h2 class="u-visually-hidden">Date:</h2>
  <span class="c-calendar-icon--dow"><?php echo $day; ?></span>
  <span class="c-calendar-icon--dom"><?php echo $multidate;?></span>
  <span class="c-calendar-icon--my"><?php echo $year; ?></span>
</time>
<!-- c-calendar-icon ends here -->
