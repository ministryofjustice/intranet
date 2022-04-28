<?php
use MOJ\Intranet\Agency;

/*
*
* Single team blog page
*
*/

get_header();

$oAgency      = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
?>

  <main role="main" id="maincontent" class="u-wrapper l-main t-team-blog-article">

    <?php
      get_template_part('src/components/c-breadcrumbs/view', 'team');
      get_template_part('src/components/c-team-blog-article/view');
    ?>

    <section class="l-full-page">
      <?php
        get_template_part('src/components/c-share-post/view');
        get_template_part('src/components/c-comments/view');
      ?>
    </section>

  </main>

<?php
get_footer();
