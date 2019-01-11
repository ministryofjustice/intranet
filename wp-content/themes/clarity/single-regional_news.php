<?php
use MOJ\Intranet\Agency;

/*
* Default single regional news (aka a news post)
*/
get_header();

$oAgency      = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
?>

<div id="maincontent" class="u-wrapper l-main t-news-article" role="main">

	<?php
	get_template_part( 'src/components/c-breadcrumbs/region', 'landing' );
	get_template_part( 'src/components/c-news-article/view', 'regional_news' );
	?>

  <section class="l-full-page">

	<?php
	get_template_part( 'src/components/c-last-updated/view' );
	get_template_part( 'src/components/c-share-post/view' );
	?>

  </section>
</div>

<?php
get_footer();
