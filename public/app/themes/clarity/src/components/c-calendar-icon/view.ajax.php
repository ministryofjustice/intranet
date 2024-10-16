<?php

/**
 * The is part of a template that is used for search results loaded via AJAX.
 * It is not wrapped in a script tag with a template attribute because it forms 
 * part of `src/components/c-events-item/view-list.ajax.php`.
 * Class names and html structure matches the view.php component.
 */

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

