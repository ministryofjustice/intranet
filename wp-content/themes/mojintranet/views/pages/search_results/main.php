<?php if (!defined('ABSPATH')) die(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post() ?>

<div class="template-container" data-top-level-slug="<?=$top_slug?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="page-title">Search results</h1>
      <p class="sr-only">The results will update automatically based on your selections.</p>
      <?php the_content() ?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-8 col-md-8 col-sm-12">
      <div role="search form" class="search-form search-string no-dw-autocomplete">
        <input class="keywords-field" autocomplete="off" type="text" placeholder="Search MoJ Intranet" name="s" value="<?=htmlspecialchars(urldecode(get_query_var('search-string')))?>">
        <!--[if IE 6 ]><img class="search-btn cta" id="search-btn" src="<?=get_template_directory_uri()?>/assets/images/search_icon_ie.png" alt="Search"/><![endif]-->
        <!--[if IE 7 ]><img class="search-btn cta" id="search-btn" src="<?=get_template_directory_uri()?>/assets/images/search_icon_ie.png" alt="Search"/><![endif]-->
        <!--[if (gt IE 7)|!(IE)]><!--><img class="search-btn cta" id="search-btn" src="<?=get_template_directory_uri()?>/assets/images/search_icon_x2.png" alt="Search"/><!--<![endif]-->
      </div>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-4 col-md-4 col-sm-12">
      <p>You can use the filters to show only results that match your interests</p>
      <form class="content-filters">
        <div class="form-row">
          <label for="input-filter-type">Filter by</label>
        </div>
        <div class="form-row">
          <select id="input-filter-date" name="type" class="search-type">
            <option value="all">All</option>
            <option value="page">Pages</option>
            <option value="document">Forms and documents</option>
          </select>
        </div>
      </form>
      <!--
      <ul class="search-type content-tabs small-tabs">
        <li data-search-type="all">
          <a href="">All</a>
        </li>
        <li data-search-type="page">
          <a href="">Pages</a>
        </li>
        <li data-search-type="document">
          <a href="">Forms and documents</a>
        </li>
      </ul>
    -->
    </div>
    <div class="col-lg-8 col-md-8 col-sm-12">
      <ul class="results"></ul>

      <ul class="content-nav grid">
        <li class="previous disabled col-lg-6 col-md-6 col-sm-6">
          <a href="#content" aria-labelledby="prev-page-label">
            <span class="nav-label" id="prev-page-label">Previous page</span>
            <span class="page-info">
              <span class="prev-page"></span>
              of
              <span class="total-pages"></span>
            </span>
          </a>

        </li>

        <li class="next disabled col-lg-6 col-md-6 col-sm-6">
          <a href="#content" aria-labelledby="next-page-label">
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

  <?php $this->view('pages/search_results/templates/search_item'); ?>
  <?php $this->view('pages/search_results/templates/search_results_page_title'); ?>
  <?php $this->view('pages/search_results/templates/serach_filtered_results_title'); ?>
</div>

<?php endwhile ?>
