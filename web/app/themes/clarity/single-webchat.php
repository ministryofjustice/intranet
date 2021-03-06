<?php
use MOJ\Intranet\Agency;

/*
* Single WebChat post
*/

if (!defined('ABSPATH')) {
    die();
}

get_header();

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

?>
  <div id="maincontent" class="u-wrapper l-main t-blog-article" role="main">
    <?php get_template_part('src/components/c-breadcrumbs/view'); ?>
    <?php get_template_part('src/components/c-article/view'); ?>

    <?php // l-full-page class provides a new block so that it is seperated from the above article where imgs can wrap into it. ?>
    <section class="l-full-page">
    <?php get_template_part('src/components/c-share-post/view'); ?>
    </section>

  </div>

<?php get_footer(); ?>
