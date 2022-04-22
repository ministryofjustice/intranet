<?php
use MOJ\Intranet\Agency;

/*
* Template Name: News - eNews archive
*
*/
if (!defined('ABSPATH')) {
    die();
}

get_header();

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

$enews = 748; // news category id for tibit

?>
  <main role="main" id="maincontent" class="u-wrapper l-main t-article-list">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    <div class="l-secondary">
        <?php get_template_part('src/components/c-content-filter/view', 'enews'); ?>
    </div>
    <div class="l-primary" role="main">
      <h2 class="o-title o-title--section" id="title-section">Latest</h2>
      <div id="content">
        <div class="data-type" data-type="news"></div>
        <?php
          get_template_part('src/components/c-news-list/view', 'enews');
        ?>
      </div>
        <?php get_pagination('news', $enews); ?>
    </div>
  </main>

<?php
get_footer();
