<?php if (!defined('ABSPATH')) die(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post() ?>

<div class="page-news">
  <h1 class="page-title"><?php the_title() ?></h1>
	<?php the_content() ?>

  <div class="row">
    <div class="col-md-4">
      &nbsp;
    </div>
    <div class="col-md-8">
      <?php $this->view('pages/tabs', array('selected'=>'news')) ?>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
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

    <div class="col-md-8">
      <?php dynamic_sidebar('newslanding-widget-area'); ?>
    </div>

  </div>
</div>

<?php endwhile ?>
