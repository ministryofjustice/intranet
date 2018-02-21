<?php if (!defined('ABSPATH')) die(); ?>

<div class="grid">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <ul class="social-actions post-social-actions">
      <?php if ($comments_on): ?>
        <li class="comments-count">
          <span class="icon"></span>
          <span class="count"></span>
        </li>
      <?php endif ?>

      <?php $id = get_the_ID(); ?>
  

      <li class="share-container">
        <span class="share-via-email-icon"></span>
        <a class="share-via-email"
           href="mailto:"
           data-title="<?=htmlspecialchars($title)?>"
           data-date="<?=htmlspecialchars($human_date)?>"
           data-body="<?=htmlspecialchars($share_email_body)?>">Share by email</a>
      </li>
  </div>
