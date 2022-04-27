<?php
use MOJ\Intranet\Agency;

/*
* Template Name: News - Tib-its archive
*/
if (! defined('ABSPATH')) {
    die();
}

get_header();

$oAgency      = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

$tibit = 749; // news category id for tibit

?>
  <main role="main" id="maincontent" class="u-wrapper l-main t-article-list">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    <div class="l-secondary">
        <?php get_template_part('src/components/c-content-filter/view', 'tibit'); ?>
    </div>
    <div class="l-primary">
      <h2 class="o-title o-title--section" id="title-section">Latest</h2>
      <div id="content">
        <div class="data-type" data-type="news"></div>
        <?php
          get_template_part('src/components/c-news-list/view', 'tibit');
        ?>
      </div>
        <?php get_pagination('news', $tibit); ?>
    </div>
  </main>

<?php
get_footer();
