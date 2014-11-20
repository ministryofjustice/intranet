<?php if (!defined('ABSPATH')) die(); ?>

<div id="need-to-know">
  <?=$data['before_widget']?>
  <h3 class="widget-title"><?=$data['title']?></h3>
  <div class="need-to-know-inner">
    <ul class="need-to-know-list">
      <?php foreach($data['items'] as $index=>$item): ?>
        <?php $this->view('news_item', $item) ?>
      <?php endforeach ?>
    </ul>
    <ul class="page-list">
      <?php for($a=1, $count=count($data['items']); $a<=$count; $a++): ?>
        <li class="item" data-page-id="<?=$a?>">
          <?=$a?>
        </li>
      <?php endfor ?>
    </ul>
  </div>
  <?=$data['after_widget']?>
</div>
