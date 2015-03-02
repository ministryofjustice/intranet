<?php if (!defined('ABSPATH')) die(); ?>

<div class="page-home">
  <?php #$this->view('pages/homepage/emergency_message', $emergency_message) ?>

  <div class="grid">
    <div class="col-lg-6 col-md-6 col-sm-12">
      <?php dynamic_sidebar('home-widget-area0'); ?>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12">
      <?php $this->view('pages/homepage/my_moj/main', $my_moj) ?>
      <?php $this->view('pages/homepage/feeds') ?>
    </div>
  </div>
</div>
