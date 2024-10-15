<?php

defined('ABSPATH') || exit;

?>

<script type="text/template" data-template="c-events-item-list">
    <div class="c-events-item-list">
        <?php
            get_template_part('src/components/c-calendar-icon/view.ajax');
            get_template_part('src/components/c-events-item-byline/view.ajax');
        ?>
    </div>
</script>
