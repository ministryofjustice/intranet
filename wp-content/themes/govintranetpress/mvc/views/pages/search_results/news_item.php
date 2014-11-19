<?php if (!defined('ABSPATH')) die(); ?>

<li class="news-item">
  <div class="thumbnail">
    <?php if($thumbnail): ?>
      <img src="<?=$thumbnail[0]?>" />
    <?php endif ?>
  </div>
  <div class="details">
    <h3><?=$title?></h3>
    <div class="meta">
      <span class="date"><?=$human_date?></span>
      <span class="category"><?=$category_name?></span>
      <span class="subcategory"><?=$subcategory_name?></span>
    </div>
    <p class="excerpt"><?=$excerpt?></p>
  </div>
</li>
