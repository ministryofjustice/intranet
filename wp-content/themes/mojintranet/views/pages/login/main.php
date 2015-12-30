<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container">
  <div class="grid">
    <div class="col-lg-8 col-md-8 col-sm-12">
    <h2>Login</h2>
      <?php if($message) { ?>
      <p class="login-message <?=$message_type?>"><?=$message?></p>
      <?php } ?>
      <?php wp_login_form($login_args); ?>
    </div>
  </div>
</div>