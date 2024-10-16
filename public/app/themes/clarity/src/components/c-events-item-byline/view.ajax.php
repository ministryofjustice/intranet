<?php

/**
 * The is part of a template that is used for search results loaded via AJAX.
 * It is not wrapped in a script tag with a template attribute because it forms 
 * part of `src/components/c-events-item/view-list.ajax.php`.
 * Class names and html structure matches the view.php component.
 */

defined('ABSPATH') || exit;

?>

<!-- c-events-item-byline starts here -->
<article class="c-events-item-byline">
  <header>

    <h3 class="c-events-item-byline__link"><a href="${permalink}">${post_title}</a></h3>

    <div class="c-events-item-byline__time">
      <span>Time:</span>
      <time datetime="${datetime_formatted}">${time_formatted}</time>
    </div>

    ${?location}
      <div class="c-events-item-byline__location">
        <span>Location:</span>
        <address>${location}</address>
      </div>
    ${/?location}

  </header>
</article>
<!-- c-events-item-byline ends here -->

