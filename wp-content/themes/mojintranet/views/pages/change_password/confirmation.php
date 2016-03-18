<?php if (!defined('ABSPATH')) die(); ?>

<div class="confirmation-screen">
  <h1>Confirmation</h1>
  <p class="confirmation-message"><?=$confirmation_message?></p>
  <a class="redirect-link" href="<?=site_url('/login')?>">Sign in</a>
</div>
