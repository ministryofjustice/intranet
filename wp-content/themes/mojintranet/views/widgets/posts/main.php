<?php if (!defined('ABSPATH')) die(); ?>

<div class="posts-widget">
  <h2 class="category-name">Blog</h2>
  <ul class="posts-list"></ul>

  <p class="see-all-container">
    <a href="<?=$see_all_posts_url?>">See all posts</a>
  </p>

  <?php $this->view('widgets/posts/post_item') ?>
</div>
