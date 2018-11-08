<?php if (!defined('ABSPATH')) die(); ?>

<ul class="main-menu-list">
  <?php foreach($main_menu['results'] as $item): ?>
    <li class="main-menu-item <?=$item['classes']?>">
      <a class="main-menu-link" href="<?=$item['url']?>"><?=$item['title']?></a>
    </li>
  <?php endforeach ?>
</ul>
