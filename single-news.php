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
  <?php get_template_part( 'src/components/c-breadcrumbs/view' ); ?>
  <?php get_template_part( 'src/components/c-article/view', 'news' ); ?>
</div>

<?php get_footer(); ?>
