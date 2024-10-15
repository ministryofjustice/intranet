<?php

defined('ABSPATH')  || exit;

?>
  
<!-- c-calendar-icon starts here -->

<div class="c-calendar-icon">
  <span class="u-visually-hidden">Date:</span>
  <time datetime="${datetime}">
    <span class="c-calendar-icon--dow">${day}</span>
    <span class="c-calendar-icon--dom">${multi_date_formatted}</span>
    <span class="c-calendar-icon--my">${year}</span>
  </time>
</div>

<!-- c-calendar-icon ends here -->

