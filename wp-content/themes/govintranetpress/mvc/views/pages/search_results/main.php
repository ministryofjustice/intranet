<?php if (!defined('ABSPATH')) die(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post() ?>

<div class="page-search-results" data-top-level-slug="<?=$top_slug?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="page-title"><?php the_title() ?></h1>
      <?php the_content() ?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-8 col-md-8 col-sm-12">
      <div class="search-string">
        <input form="search-form" type="text" placeholder="Keywords" name="keywords" class="main-search-field" value="<?=get_query_var('search-string')?>" />
        <input form="search-form" type="submit" class="cta search-btn" />
      </div>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-3 col-md-3 col-sm-12">
      <form id="search-form" class="content-filters">
        <p class="description">You can use the filters to show only results that match your interests</p>
        <div class="form-row">
          <span class="label">Filter by</span>
        </div>
        <div class="form-row">
          <select name="category">
            <option value="All">All</option>
          </select>
        </div>
        <div class="form-row contains">
          <span class="label">Type of pages</span>
        </div>
        <div class="form-row">
          <select name="type">
            <option value="All">All</option>
          </select>
        </div>
      </form>
    </div>

    <div class="col-lg-8 col-md-8 col-sm-12 push-lg-1 push-md-1">
      <ul class="results"></ul>

      <ul class="content-nav grid">
        <li class="previous disabled col-lg-6 col-md-6 col-sm-6">
          <a href="#">
            <span class="nav-label">Previous page</span>
            <span class="page-info">
              <span class="prev-page"></span>
              of
              <span class="total-pages"></span>
            </span>
          </a>

        </li>

        <li class="next disabled col-lg-6 col-md-6 col-sm-6">
          <a href="#">
            <span class="nav-label">Next page</span>
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

  <div class="template-partial" data-name="search-item">
    <li class="search-item">
      <a href="" class="search-link">
        <h3 class="title"></h3>
      </a>
      <div class="meta grid">
        <div class="col-lg-3">
          <span class="date"></span>
        </div>
        <div class="col-lg-3">
          <span class="type">Guidance</span>
        </div>
      </div>
      <p class="excerpt"></p>
      <p class="file">
        Download
        <a class="file-link" href=""></a>,
        <span class="file-size"></span>,
        <span class="file-length"></span>
        Pages
      </p>
    </li>
  </div>

  <div class="template-partial" data-name="search-results-page-title">
    <h3 class="search-results-page-title search-results-title">Latest</h3>
  </div>

  <div class="template-partial" data-name="search-filtered-results-title">
    <h3 class="search-filtered-results-title search-results-title">
      <span class="results-count"></span>
      <span class="results-count-description"></span>
      <span class="containing">containing</span>
      <span class="keywords"></span>
    </h3>
  </div>
</div>

<?php endwhile ?>
