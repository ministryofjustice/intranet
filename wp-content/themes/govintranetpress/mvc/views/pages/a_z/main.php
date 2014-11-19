<?php if (!defined('ABSPATH')) die(); ?>

<div class="a-z" data-top-level-slug="<?=$top_slug?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <ul class="tabbed-filters">
        <li class="selected alpha">
          <a href="">
            <span class="icon"></span>
            <span class="label">Pages</span>
          </a>
        </li>
        <li class="document">
          <a href="">
            <span class="icon"></span>
            <span class="label">Forms &amp; templates</span>
          </a>
        </li>
      </ul>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">
      <h2><?=$title?></h2>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="grid">
        <form class="content-filters content-filters-horizontal">
          <div class="col-lg-6">
            <div class="form-row">
              <span class="label">Filter by</span>
            </div>
            <div class="form-row">
              <select name="category">
                <option>All</option>
                <option>Category 1</option>
                <option>Category 2</option>
              </select>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-row">
              <span class="label">Contains</span>
            </div>
            <div class="form-row">
              <input type="text" placeholder="Keywords" name="keywords" />
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">
      <ul class="letters">
        <?php foreach($letters as $letter): ?>
          <li>
            <?=$letter?>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
</div>
