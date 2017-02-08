<?php if (!defined('ABSPATH')) die(); ?>

<div class="posts-widget" data-posts-type="<?=$type?>">
  <h2 class="category-name">Blog</h2>

  <div class="grid">
    <?php for($a = 1; $a <= $number_of_lists; $a++): ?>
      <div class="<?=$list_container_classes?>">
        <ul class="posts-list"
            data-skeleton-screen-count="<?=$skeleton_screen_count?>"
            data-skeleton-screen-type="standard"></ul>
      </div>
    <?php endfor ?>
  </div>

  <p class="no-posts-message">
    <?=$no_posts_found_message?>
  </p>
  <p class="see-all-container">
    <a href="<?=$see_all_url?>"><?=$see_all_label?></a>
  </p>

  <?php $this->view('widgets/posts/post_item') ?>
</div>
