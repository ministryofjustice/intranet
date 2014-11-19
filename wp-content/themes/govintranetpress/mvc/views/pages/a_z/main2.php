<?php if (!defined('ABSPATH')) die(); ?>

<div class="a-z-2">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h2><?=$title?></h2>
    </div>

    <div class="col-lg-3 filters-column">
      <form class="content-filters content-filters-horizontal">
        <div class="form-row">
          <span class="label">Type</span>
        </div>
        <div class="form-row">
          <select name="category">
            <option>Pages</option>
            <option>Forms &amp; templates</option>
          </select>
        </div>
        <div class="form-row">
          <span class="label">Category</span>
        </div>
        <div class="form-row">
          <select name="category">
            <option>All</option>
            <option>Category 1</option>
            <option>Category 2</option>
          </select>
        </div>
        <div class="form-row">
          <span class="label">Contains</span>
        </div>
        <div class="form-row">
          <input type="text" placeholder="Keywords" name="keywords" />
        </div>
      </form>
    </div>
    <div class="col-lg-9">
      <ul class="letters">
        <?php foreach($letters as $letter): ?>
          <li>
            <?=$letter?>
          </li>
        <?php endforeach ?>
      </ul>

      <ul class="results">
        <?php foreach($results as $result): ?>
          <li class="result grid">
            <h4 class="title col-lg-3"><?=$result['title']?></h4>
            <p class="description col-lg-5"><?=$result['description']?></p>
          </li>
        <?php endforeach ?>
      </ul>

      <ul class="content-nav grid">
        <li class="previous col-lg-6 col-md-6 col-sm-6">
          <a href="<?=$prev_news_url ?: '#'?>">
            <span class="nav-label">&lsaquo; Previous page</span>
            <span class="page-number">2 of 35</span>
          </a>

        </li>

        <li class="next col-lg-6 col-md-6 col-sm-6">
          <a href="<?=$next_news_url ?: '#'?>">
            <span class="nav-label">Next page &rsaquo;</span>
            <span class="page-number">4 of 35</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
