<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container">
  <div class="grid">
    <div class="col-lg-8 col-md-8 col-sm-12">
      <?php $this->view('pages/change_password/form', $tpl) ?>
      <?php $this->view('pages/change_password/confirmation', $tpl) ?>
    </div>
  </div>
</div>
