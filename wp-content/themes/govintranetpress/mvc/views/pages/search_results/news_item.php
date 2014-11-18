<?php if (!defined('ABSPATH')) die(); ?>

<li class="news-item">
  <div class="thumbnail">
    <img src="<?=$thumbnail[0]?>" />
  </div>
  <div class="details">
    <h3><?=$title?></h3>
    <div>
      <span><?=$human_date?></span>
      <span><?=$category_name?></span>
      <span><?=$subcategory_name?></span>
    </div>
    <p class="excerpt"><?=$excerpt?></p>
  </div>
</li>
