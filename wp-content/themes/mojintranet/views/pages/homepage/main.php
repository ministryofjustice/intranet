<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container">
  <?php $this->view('pages/homepage/emergency_message', $emergency_message) ?>

  <div class="grid">
    <div class="col-lg-8 col-md-6 col-sm-12">
      News
    </div>

    <div class="col-lg-4 col-md-6 col-sm-12">
      <?php $this->view('pages/homepage/my_moj/main', $my_moj) ?>
      <?php $this->view('widgets/posts/main') ?>
    </div>


    <?php #dynamic_sidebar('home-widget-area0'); ?>
    <?php #$this->view('widgets/events', array('events' => $events, 'see_all_events_url' => $see_all_events_url)) ?>
  </div>

  <div class="grid">
    <div class="col-lg-8 col-md-6 col-sm-12">
      <?php $this->view('widgets/events/main') ?>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
      <?php $this->view('pages/homepage/social') ?>
    </div>
  </div>
</div>
