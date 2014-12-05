<?php if (!defined('ABSPATH')) die(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post() ?>

<div class="page-news">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="page-title"><?php the_title() ?></h1>
      <?php the_content() ?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-3 col-md-3 mobile-hide">&nbsp;</div>
    <div class="col-lg-8 col-md-8 col-sm-12 push-lg-1 push-md-1">
      <?php dynamic_sidebar('newslanding-widget-area0'); ?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-3 col-md-3 col-sm-12">
      <form class="content-filters">
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
        <div class="form-row contains">
          <span class="label">Contains</span>
        </div>
        <div class="form-row">
          <input type="text" placeholder="Keywords" name="keywords" />
        </div>
      </form>
    </div>

    <div class="col-lg-8 col-md-8 col-sm-12 push-lg-1 push-md-1">
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
</div>

<?php endwhile ?>
