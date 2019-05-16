<?php
  use MOJ\Intranet\Agency;

  $oAgency      = new Agency();
  $activeAgency = $oAgency->getCurrentAgency();

if ( $activeAgency['shortcode'] === 'law-commission' ) {
	$logo = get_stylesheet_directory_uri() . '/assets/images/lawcomms_logo.png';
} else {
	$logo = get_stylesheet_directory_uri() . '/assets/images/moj_logo.png';
}

  $page_name = get_query_var( 'name' );
?>

<section class="c-logo-bar">
  <div class="u-wrapper">

		<div class="u-wrapper__stack--left">
			<a href="/" rel="home">
			<img src="<?php echo $logo; ?>" alt="<?php echo $activeAgency['label']; ?> Logo">
			<span class="agency-title l-half-section"><?php echo $activeAgency['label']; ?></span>
			</a>
		</div>

		<div class="u-wrapper__stack--right">
			<?php if ( $page_name !== 'agency-switcher' ) : ?>
			<a href="/agency-switcher" class="c-logo-bar__switch">Switch to other intranet</a>
			<?php endif; ?>
		</div>
		
	</div>
  </div>
</section>
