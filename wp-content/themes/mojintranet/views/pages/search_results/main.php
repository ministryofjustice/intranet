<?php if (!defined('ABSPATH')) die(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post() ?>

<div class="template-container"
  data-top-level-slug="<?=$top_slug?>"
  data-dw-tag="<?=$dw_tag?>"
  data-resource-categories="<?=$resource_categories?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="page-title">Search results</h1>
      <p class="sr-only">The results will update automatically based on your selections.</p>
      <?php the_content() ?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-8 col-md-8 col-sm-12">
      <div role="search form" class="search-form search-string no-dw-autocomplete ">
        <div class="keywords-field-container">
          <input class="keywords-field" autocomplete="off" type="text" placeholder="Search MoJ Intranet" name="s" value="<?=htmlspecialchars(urldecode(get_query_var('search-string')))?>">
          <!--[if lte IE 7 ]><img class="search-btn cta" id="search-btn" src="<?=get_template_directory_uri()?>/assets/images/search_icon_ie.png" alt="Search"/><![endif]-->
          <!--[if (gt IE 7)|!(IE)]><!--><img class="search-btn cta" id="search-btn" src="<?=get_template_directory_uri()?>/assets/images/search_icon_x2.png" alt="Search"/><!--<![endif]-->
        </div>
      </div>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-4 col-md-4 col-sm-12">
      <form class="content-filters">
        <p class="description">You can use the filters to show only results that match your interests</p>
        <div class="form-row">
          <label class="filter-label" for="input-filter-type">Content type</label>
        </div>
        <div class="form-row">
          <select id="input-filter-date" name="type" class="search-type">
            <option value="all">All</option>
            <option value="content">Pages</option>
            <option value="document">Forms and documents</option>
          </select>
        </div>

        <div class="resource-categories-box">
          <div class="form-row">
            <label class="filter-label" for="input-filter-categories">Categories</label>
          </div>
          <div class="form-row">
            <select name="categories[]" multiple></select>
          </div>
        </div>
      </form>
      <p class="description">To search news go to the <a href="<?=get_permalink(get_page_by_path('newspage'))?>">News</a> page</p>
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
  <?php $this->view('pages/search_results/templates/search_filtered_results_title'); ?>
</div>

<?php endwhile ?>
