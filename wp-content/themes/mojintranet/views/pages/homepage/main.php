<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container">
  <?php $this->view('pages/homepage/emergency_message') ?>

  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="page-title">Ministry of Justice HQ</h1>
    </div>
    <div class="col-lg-8 col-md-6 col-sm-12">
      <?php $this->view('widgets/featured_news/main') ?>
      <?php $this->view('widgets/news_list/main', $news_widget) ?>
      <?php $this->view('widgets/need_to_know/main') ?>
      <?php $this->view('widgets/events/main', $events_widget) ?>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-12">
      <?php $this->view('pages/homepage/my_moj/main') ?>
      <?php $this->view('widgets/posts/main') ?>
      <?php $this->view('pages/homepage/social') ?>
    </div>
  </div>
</div>
