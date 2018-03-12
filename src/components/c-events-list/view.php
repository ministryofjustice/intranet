<?php
use MOJ\Intranet\Event;

if (!defined('ABSPATH')) {
    die();
}

?>
<!-- c-events-list starts here -->
<div class="c-events-list">
    <?php
      if (is_front_page()) {
          get_template_part('src/components/c-events-item/view', 'homepage');
      } else {
          get_template_part('src/components/c-events-item/view', 'list');
      }
    ?>
</div>
<!-- c-events-list ends here -->
