<?php
use MOJ\Intranet\Agency;

/*
* Template Name: Blog archive
*/
if (!defined('ABSPATH')) {
    die();
}

get_header();

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

?>
  <main role="main" id="maincontent" class="u-wrapper l-main t-article-list">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    <div class="l-secondary">
      <?php get_template_part('src/components/c-content-filter/view'); ?>
    </div>
    <div class="l-primary">
      <h2 class="o-title o-title--section" id="title-section">Latest</h2>
      <div id="content">
        <?php get_post_api(); ?>
      </div>
        <?php get_pagination('posts'); ?>
    </div>
  </main>

<?php
get_footer();
