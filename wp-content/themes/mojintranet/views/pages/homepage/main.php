<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container">
  <?php $this->view('pages/homepage/emergency_message', $emergency_message) ?>

  <div class="grid">
    <div class="col-lg-8 col-md-6 col-sm-12">
      <?php $this->view('widgets/featured_news/main') ?>
      <?php $this->view('widgets/news_list/main') ?>
      <?php $this->view('widgets/need_to_know/main') ?>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-12">
      <?php $this->view('pages/homepage/my_moj/main', $my_moj) ?>
      <?php $this->view('widgets/posts/main') ?>
    </div>
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
