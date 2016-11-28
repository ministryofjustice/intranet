<?php if (!defined('ABSPATH')) die(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post() ?>

<div class="template-container"
  data-template-uri="<?=get_template_directory_uri()?>"
  data-page-base-url="<?=$page_base_url?>"
  data-news-categories="<?=$news_categories?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="page-title"><?php the_title() ?></h1>
      <?php the_content() ?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-4 col-md-4 col-sm-12">
      <h3 class="filters-label">Filter results</h3>
      <form class="content-filters">
        <p class="description">The results will update automatically based on your selections.</p>
        <div class="form-row">
          <label class="filter-label" for="input-filter-date">Date</label>
        </div>
        <div class="form-row">
          <select name="date" id="input-filter-date">
            <option value="">All</option>
          </select>
        </div>

        <div class="form-row contains">
          <label class="filter-label" for="input-filter-contains">Keywords</label>
        </div>
        <div class="form-row">
          <input type="text" placeholder="Keywords" name="keywords" id="input-filter-contains" />
        </div>

        <div class="news-categories-box">
          <div class="form-row">
            <label class="filter-label" for="input-filter-categories">Categories</label>
          </div>
          <div class="form-row">
            <select name="categories[]" multiple></select>
          </div>
        </div>
      </form>
    </div>

    <div class="col-lg-8 col-md-8 col-sm-12">
      <ul class="results"></ul>

      <ul class="content-nav grid">
        <li class="previous disabled col-lg-6 col-md-6 col-sm-6">
          <a href="" aria-labelledby="prev-page-label">
            <span class="nav-label" id="prev-page-label">Previous page</span>
            <span class="page-info">
              <span class="prev-page"></span>
              of
              <span class="total-pages"></span>
            </span>
          </a>

        </li>

        <li class="next disabled col-lg-6 col-md-6 col-sm-6">
          <a href="" aria-labelledby="next-page-label">
            <span class="nav-label" id="next-page-label">Next page</span>
            <span class="page-info">
              <span class="next-page"></span>
              of
              <span class="total-pages"></span>
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <?php $this->view('modules/news_landing') ?>
</div>

<?php endwhile ?>
