<?php if (!defined('ABSPATH')) die(); ?>

<div class="grid">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <ul class="social-actions post-social-actions">

      <?php $id = get_the_ID(); ?>

      <li class="share-container">
        <span class="share-via-email-icon"></span>
        <a href="mailto:?subject=<?php echo get_the_title(); ?>&body=A colleague thought you would be intrested in this intranet article <?php echo get_permalink(); ?>">Share this post by email</a>
      </li>
  </div>
