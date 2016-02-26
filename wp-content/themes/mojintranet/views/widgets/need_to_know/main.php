<?php if (!defined('ABSPATH')) die(); ?>

<div class="need-to-know-widget">

  <ul class="slide-list"></ul>

  <div class="need-to-know-pagination">
    <span class="need-to-know-page-indicator"></span>
    <span class="need-to-know-page-controls">
      <span class="nav-arrow left-arrow"></span>
      <span class="nav-arrow right-arrow"></span>
    </span>
  </div>

  <?php $this->view('widgets/need_to_know/need_to_know_item') ?>
</div>
