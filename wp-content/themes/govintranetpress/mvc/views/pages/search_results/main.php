<?php if (!defined('ABSPATH')) die(); ?>

<div class="news-archive">
  <div class="grid">
    <div class="col-lg-4 col-md-4 col-sm-12">
      <form class="content-filters">
        <div class="form-row">
          <span class="label">Filter by:</span>
        </div>
        <div class="form-row">
          <select name="category">
            <option>All</option>
            <option>Category 1</option>
            <option>Category 2</option>
          </select>
        </div>
        <div class="form-row">
          <input type="text" placeholder="Keywords" name="keywords" />
        </div>
      </form>
    </div>
    <div class="col-lg-8 col-md-8 col-sm-12">
      <ul class="news-list">
        <?php foreach($posts as $post_data): ?>
          <?php $this->view('pages/search_results/news_item', $post_data); ?>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
</div>
