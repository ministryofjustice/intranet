<?php

if (!defined('ABSPATH')) {
    die();
}
?>
<time class="c-calendar-icon" datetime="<?php echo $date;?>">
  <h2 class="u-visually-hidden">Date:</h2>
  <span class="c-calendar-icon--dow"><?php echo $day; ?></span>
  <span class="c-calendar-icon--dom"><?php echo $date;?></span>
  <span class="c-calendar-icon--my"><?php echo $year; ?></span>
</time>
