<time class="c-calendar-icon" datetime="<?php echo $data;?>">
  <h2 class="u-visually-hidden">Date:</h2>
  <span class="c-calendar-icon--dow"><?php echo date("l", strtotime($data));?></span>
  <span class="c-calendar-icon--dom"><?php echo date("d", strtotime($data));?></span>
  <span class="c-calendar-icon--my"><?php echo date("M Y", strtotime($data));?></span>
</time>
