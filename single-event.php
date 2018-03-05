<?php
use MOJ\Intranet\Agency;

/*
* Single event page
*/

if (!defined('ABSPATH')) {
    die();
}

get_header();

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

?>
  <div id="maincontent" class="u-wrapper l-main t-events">
    <?php get_template_part('src/components/c-breadcrumbs/view'); ?>
    <?php get_template_part('src/components/c-article/view', 'events'); ?>

    <section class="l-full-page">
    <?php get_template_part('src/components/c-share-post/view'); ?>
    </section>

  </div>
<?php get_footer(); ?>
