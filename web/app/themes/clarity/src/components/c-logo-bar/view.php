<?php
  use MOJ\Intranet\Agency;

  $oAgency      = new Agency();
  $activeAgency = $oAgency->getCurrentAgency();

if ($activeAgency['shortcode'] === 'law-commission') {
    $logo = get_stylesheet_directory_uri() . '/dist/images/lawcomms_logo_new.png';
} else {
	$logo = get_stylesheet_directory_uri() . '/dist/images/moj_logo_new.png';
}

$agency = get_intranet_code();

$header_logo  = get_field($agency .'_header_logo', 'option');

if(empty($header_logo) == false){
    $logo = $header_logo;
}

  $page_name = get_query_var('name');
?>

<section class="c-logo-bar">
  <div class="u-wrapper">

        <div class="u-wrapper__stack--left">
            <a href="/" rel="home">
            <img aria-hidden="true" src="<?php echo $logo; ?>" alt>
            <span class="agency-title l-half-section"><?php echo $activeAgency['label']; ?></span>
            </a>
        </div>

        <div class="u-wrapper__stack--right">
            <?php if ($page_name !== 'agency-switcher') : ?>
            <a href="/agency-switcher" class="c-logo-bar__switch">Switch to other intranet</a>
            <?php endif; ?>
        </div>
        
    </div>
  </div>
</section>
