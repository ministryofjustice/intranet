<?php if (!defined('ABSPATH')) die(); ?>

<div class="grid">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <nav class='breadcrumbs'>
      <?php if(function_exists('bcn_display') && !is_front_page()) {
        bcn_display();
      }?>
    </nav>
  </div>
</div>
