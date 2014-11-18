<?php if (!defined('ABSPATH')) die(); ?>

<div class="a-z">
  <div class="grid">
    <div class="col-lg-8 col-md-12 col-sm-12">
      <h2>Search results for</h2>
      <div class="input-group">
        <input type="text" class="form-control" name="s" id="s"">
        <button class="search-btn cta" type="submit"></button>
      </div>
    </div>
  </div>
  <div class="grid">
    <div class="col-lg-4 col-md-4 col-sm-12">
      <form class="filters">
        <p class="description">You can use the filters to show only results that match your interests</p>
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
      </form>
    </div>

    <div class="col-lg-8 col-md-8 col-sm-12">
      <div class="results">
        <p class="description">20 results found</p>
        <ul class="results-list">
          <?php foreach($results as $key=>$result): ?>
            <li>
              <h3>
                <a href="#"><?=$result['title']?></a>
              </h3>
              <div class="meta">
                <span class="date"><?=$result['human_date']?></span>
                <span class="breadcrumbs"><?=$result['breadcrumbs']?></span>
                <span class="category"><?=$result['category']?></span>
              </div>
              <p class="description"><?=$result['description']?></p>
            </li>
          <?php endforeach ?>
        </ul>

        <ul class="content-nav">
          <li class="previous">
            <span>
              <? if($prev_page_exists): ?>
                <a href="<?=$prev_page_url?>">
                  Previous
                </a>
              <? else: ?>
                Previous
              <? endif ?>
            </span>
          </li>

          <li class="next">
            <span>
              <? if($next_page_exists): ?>
                <a href="<?=$next_page_url?>">
                  Next
                </a>
              <? else: ?>
                Next
              <? endif ?>
            </span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
