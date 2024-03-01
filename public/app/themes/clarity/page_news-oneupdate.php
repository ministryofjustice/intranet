<?php
use MOJ\Intranet\Agency;

/*
* Template Name: News - One Update archive
*/
if (!defined('ABSPATH')) {
    die();
}
$oneupdates = 1257; // news category id for tibit
get_header();

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

?>
  <main role="main" id="maincontent" class="u-wrapper l-main t-article-list">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    <div class="l-secondary">
    </div>
    <div class="l-primary">
      <h2 class="o-title o-title--section" id="title-section">Latest</h2>
      <div id="content">
        <div class="data-type" data-type="news"></div>
        <?php
          get_template_part('src/components/c-news-list/view', 'oneupdate');
        ?>
      </div>
        <?php get_pagination('news', $oneupdates); ?>
    </div>
  </main>

<?php
get_footer();
