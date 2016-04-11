<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container" data-top-level-slug="<?=$top_slug?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1><?=$title?></h1>
      <div class="excerpt">
        <?=$excerpt?>
      </div>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="guidance-categories">
        <?php /*dynamic_sidebar('guidance-index');*/ ?>
      </div>
    </div>
  </div>
</div>
