<?php
use MOJ\Intranet\Event;

if (!defined('ABSPATH')) {
    die();
}

?>
<!-- c-events-list starts here -->
<section class="c-events-list">
    <?php get_template_part('src/components/c-events-item/view', 'list'); ?>
</section>
<!-- c-events-list ends here -->
