<?php if (!defined('ABSPATH')) die(); ?>

<div class="posts-widget">
  <h2 class="category-name">Blog</h2>
  <ul class="posts-list"
      data-use-skeleton-screens="true"
      data-skeleton-screen-count="5"
      data-skeleton-screen-type="standard"></ul>

  <p class="no-posts-message">
    No blog posts found
  </p>
  <p class="see-all-container">
    <a href="<?=get_permalink(Taggr::get_id('blog-landing'))?>">See all posts</a>
  </p>

  <?php $this->view('widgets/posts/post_item') ?>
</div>
