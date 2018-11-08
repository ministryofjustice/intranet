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
          <div class="col-lg-4">
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
          </div>
          <div class="col-lg-4">
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
          <li class="letter" data-letter="<?=$letter?>">
            <a href="#"><?=$letter?></a>
          </li>
        <?php endforeach ?>
      </ul>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">
      <ul class="results"></ul>

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

  <template data-name="a-z-result-item">
    <li class="result grid">
      <h4 class="title col-lg-3"></h4>
      <p class="description col-lg-9"></p>
    </li>
  </template>

  <template data-name="a-z-results-initial">
    <li class="result grid">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <p>No results found.</p>
      </div>
    </li>
  </template>

  <template data-name="a-z-no-results">
    <li class="no-results grid">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <p>No results found.</p>
      </div>
    </li>
  </template>
</div>
