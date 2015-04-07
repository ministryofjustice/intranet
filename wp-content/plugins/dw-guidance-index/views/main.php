<?php if (!defined('ABSPATH')) die(); ?>

<div style="clear: both">
</div>
<div class="guidance-index-widget">
  <ul class="guidance-categories-list large">
    <? foreach($menu_data['large_menu'] as $category): ?>
      <?php $this->view('category_item', $category) ?>
    <? endforeach ?>
  </ul>

  <ul class="guidance-categories-list small">
    <? foreach($menu_data['small_menu'] as $category): ?>
      <?php $this->view('category_item', $category) ?>
    <? endforeach ?>
  </ul>
</div>
