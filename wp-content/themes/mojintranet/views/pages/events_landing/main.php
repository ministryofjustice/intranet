<?php if (!defined('ABSPATH')) die(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post() ?>

<div class="template-container" data-top-level-slug="<?=$top_slug?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="page-title"><?php the_title() ?></h1>
      <?php the_content() ?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-4 col-md-4 mobile-hide">&nbsp;</div>
    <div class="col-lg-8 col-md-8 col-sm-12">
      <ul role="tablist" class="content-tabs static">
        <?php foreach($tabs as $tab): ?>
          <li class="<?= $tab['selected'] ? 'current-menu-item' : '' ?>">
            <a href="<?=$tab['url']?>">
              <?=$tab['label']?>
            </a>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-4 col-md-4 col-sm-12">
      <h3 class="filters-label">Filter results</h3>
      <form class="content-filters">
        <p class="description">The results will update automatically based on your selections.</p>
        <div class="form-row">
          <label for="input-filter-date">Date</label>
        </div>
        <div class="form-row">
          <select name="date" id="input-filter-date">
            <option value="">All</option>
          </select>
        </div>
        <div class="form-row contains">
          <label for="input-filter-contains">Keywords</label>
        </div>
        <div class="form-row">
          <input type="text" placeholder="Keywords" name="keywords" id="input-filter-contains" />
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

  <div class="template-partial" data-name="events-item">
    <li class="results-item">
      <div class="item-row">
        <time class="date-box" datetime="">
          <span class="day-of-week"></span>
          <span class="day-of-month"></span>
          <span class="month-year"></span>
        </time>
        <div class="content">
          <h3 class="title">
            <a href="" class="results-link"></a>
          </h3>
          <div class="meta">
            <ul>
              <li class="meta-date">
                <span class="label">Date:</span><span class="value"></span>
              </li>
              <li class="meta-time">
                <span class="label">Time:</span><span class="value"></span>
              </li>
              <li class="meta-location">
                <span class="label">Location:</span><span class="value"></span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <span class="ie-clear"></span>
    </li>
  </div>

  <div class="template-partial" data-name="events-results-page-title">
    <h2 class="results-page-title results-title">Latest</h2>
  </div>

  <div class="template-partial" data-name="events-filtered-results-title">
    <h2 class="filtered-results-title results-title">
      <span class="results-count"></span>
      <span class="results-count-description"></span>
      <span class="containing">containing</span>
      <span class="keywords"></span>
      <span class="for-date">for</span>
      <span class="date"></span>
    </h2>
  </div>
</div>

<?php endwhile ?>
