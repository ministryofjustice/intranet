<?php
use MOJ\Intranet\Agency;

/*
* Template Name: News - One Update archive page
*/
if (!defined('ABSPATH')) {
    die();
}
$oneupdates = 1257;
get_header();

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

?>
  <div id="maincontent" class="u-wrapper l-main t-article-list">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    <div class="l-secondary">

    </div>
    <div class="l-primary" role="main">
      <h2 class="o-title o-title--section" id="title-section">Latest</h2>
      <div id="content">
        <?php get_category_news_api($oneupdates); ?>
      </div>
    </div>
  </div>

<?php get_footer(); ?>
