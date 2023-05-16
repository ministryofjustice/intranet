<?php

if (! defined('ABSPATH')) {
    die();
}

$start_date = $start_date ?? '';
$end_date = $end_date ?? '';
$datetime = $datetime ?? '';
$year = $year ?? '';
$day = $day ?? '';

// If start date and end date selected are the same, just display first date.
if ($start_date === $end_date) {
    $multi_date = date('d M', strtotime($start_date));
} else {
    $multi_date = date('d M', strtotime($start_date)) . ' - ' . date('d M', strtotime($end_date));
}
?>

<!-- c-calendar-icon starts here -->

<div class="c-calendar-icon">
  <span class="u-visually-hidden">Date:</span>
  <time datetime="<?= $datetime ?>">
    <span class="c-calendar-icon--dow"><?= $day ?></span>
    <span class="c-calendar-icon--dom"><?= $multi_date ?></span>
    <span class="c-calendar-icon--my"><?= $year ?></span>
  </time>
</div>
<!-- c-calendar-icon ends here -->
