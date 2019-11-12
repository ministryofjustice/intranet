<?php
use MOJ\Intranet\Agency;

$agency = get_intranet_code();
$polls  = get_field($agency . '_homepage_polls_shortcode', 'option');
?>
<!-- c-polls starts here -->
<section class="c-polls js-polls">
    <?php
    echo do_shortcode($polls);
    ?>
  <br/>
</section>
<!-- c-polls ends here -->
