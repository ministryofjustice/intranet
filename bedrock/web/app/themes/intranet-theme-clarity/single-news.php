<?php
use MOJ\Intranet\Agency;

/*
* Single news post
*/

if (!defined('ABSPATH')) {
    die();
}

get_header();

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

?>

<div id="maincontent" class="u-wrapper l-main t-news-article" role="main">
  <?php get_template_part('src/components/c-breadcrumbs/view'); ?>
  <?php get_template_part('src/components/c-news-article/view'); ?>

  <section class="l-full-page">
  <?php get_template_part('src/components/c-share-post/view'); ?>
  </section>
</div>

<?php get_footer(); ?>
