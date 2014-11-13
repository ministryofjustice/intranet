<?php if (!defined('ABSPATH')) die(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post() ?>

<div class="page-news">
  <div class="grid">
    <div class="col-lg-12">
      <h1 class="page-title"><?php the_title() ?></h1>
      <?php the_content() ?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-4">&nbsp;</div>
    <div class="col-lg-8">
      <?php dynamic_sidebar('newslanding-widget-area0'); ?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-4">
      <form class="news-filters">
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
        <div class="form-row">
          <input type="submit" value="Search" />
        </div>
      </form>
    </div>

    <div class="col-lg-8">
      <?php dynamic_sidebar('newslanding-widget-area1'); ?>
    </div>
  </div>
</div>

<?php endwhile ?>
