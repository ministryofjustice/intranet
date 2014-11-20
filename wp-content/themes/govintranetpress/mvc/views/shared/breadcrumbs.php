<?php if (!defined('ABSPATH')) die(); ?>

<div class="row">
  <div class='breadcrumbs'>
    <?php if(function_exists('bcn_display') && !is_front_page()) {
      bcn_display();
    }?>
  </div>
</div>
