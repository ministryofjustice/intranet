<?php
use MOJ\Intranet\Agency;

/*
* Default single regional news (aka a news post)
*/
get_header();

$oAgency      = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
?>

<main role="main" id="maincontent" class="u-wrapper l-main t-news-article">

    <?php
      get_template_part('src/components/c-breadcrumbs/view', 'region-single');
      get_template_part('src/components/c-news-article/view', 'regional_news');
    ?>

  <section class="l-full-page">

    <?php
      get_template_part('src/components/c-last-updated/view');
      get_template_part('src/components/c-share-post/view');
    ?>

  </section>
</main>

<?php
get_footer();
