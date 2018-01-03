<?php
use MOJ\Intranet\Agency;
/*
* Template Name: Blog archive page
*/
if (!defined('ABSPATH')) {
    die();
}

get_header();

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

?>
  <div id="maincontent" class="u-wrapper l-main l-reverse-order t-article-list">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    <div class="l-secondary">
      <?php get_template_part('src/components/c-content-filter/view'); ?>
    </div>
    <div class="l-primary" role="main">
      <h2 class="o-title o-title--section">Latest</h2>
      <?php get_post_api(); ?>
      <div id="load_more">
       
      </div>
      <a href="#" class="more-btn" data-page="1">More button</a>
      
      <?php //get_template_part('src/components/c-pagination/view'); ?>
    </div>
  </div>

<?php get_footer(); ?>
