<?php if (!defined('ABSPATH')) die(); ?>

<div class="confirmation-screen">
  <h1><?=$confirmation_title_text?></h1>
  <p class="confirmation-message"><?=$confirmation_message_text?></p>
  <a class="redirect-link" href="<?=site_url('/login')?>">Go to sign in</a>
</div>
