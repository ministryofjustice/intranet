<?php if (!defined('ABSPATH')) die(); ?>

<li class="category-item <?=$type?>">
  <h3 class="category-title">
    <a href="<?=$url?>">
      <?=$title?>
    </a>
  </h3>

  <ul class="children-list">
    <?php foreach($children as $child): ?>
      <?php $this->view('child_item', $child); ?>
    <?php endforeach ?>
  </ul>
</li>
