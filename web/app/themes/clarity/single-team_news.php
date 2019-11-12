<?php
use MOJ\Intranet\Agency;

/*
* Single team news post
*/

get_header();

$oAgency      = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

?>

<div id="maincontent" class="u-wrapper l-main t-news-article" role="main">
    <?php
    get_template_part('src/components/c-breadcrumbs/view', 'team');
    get_template_part('src/components/c-news-article/view', 'full');
    ?>

  <section class="l-full-page">
    <?php get_template_part('src/components/c-share-post/view'); ?>
  </section>
</div>

<?php
get_footer();
