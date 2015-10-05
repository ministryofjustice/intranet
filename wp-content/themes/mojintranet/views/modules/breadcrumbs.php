<?php if (!defined('ABSPATH')) die(); ?>

<div class="breadcrumbs">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <ul class="breadcrumbs-list">
        <?php foreach($breadcrumbs as $breadcrumb): ?>
          <li class="breadcrumb-item <?=$breadcrumb['last'] ? 'current' : ''?>">
            <?php if(!$breadcrumb['last']): ?>
              <a class="breadcrumb-link" href="<?=$breadcrumb['url']?>">
                <?=$breadcrumb['title']?>
              </a>
              <span class="separator"></span>
            <?php else: ?>
              <?=$breadcrumb['title']?>
            <?php endif ?>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
</div>
