<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container">
  <div class="main-screen">
    <div class="grid">
      <div class="col-lg-8 col-md-12 col-sm-12">
        <div class="panel error-panel">
          <h1>Your email link has expired</h1>

          <p>Request a new link to begin commenting.</p>
        </div>
      </div>
    </div>

    <?php $this->view('modules/request_activation_link/form') ?>
  </div>

  <?php $this->view('pages/user/activate/request_activation_link_confirmation') ?>
</div>
