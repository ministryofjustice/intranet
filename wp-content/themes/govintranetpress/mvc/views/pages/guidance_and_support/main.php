<?php if (!defined('ABSPATH')) die(); ?>

<div class="guidance-and-support" data-top-level-slug="<?=$top_slug?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h3><?=$title?></h3>
      <form role="search form" class="search-form" name="search-form" method="post" action="<?=site_url()?>/search-results/" >
        <input class="keywords-field ui-autocomplete-input" type="text" placeholder="Search" name="s" id="s" value="<?=urldecode(get_query_var('search-string'))?>">
        <input class="search-btn cta" type="submit" value="Search" />
      </form>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="guidance-categories">
        <?php dynamic_sidebar('guidance-index'); ?>
      </div>
    </div>
  </div>
</div>
