<?php

use MOJ\Intranet\Agency;

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

// Show a simplified header if the user has not yet chosen an agency
$simpleHeader = !$oAgency->hasAgencyCookie();

$header_logo = get_field(get_intranet_code() .'_header_logo', 'option');
$logo = get_stylesheet_directory_uri() . '/dist/images/moj_logo_header.png';

# If the agency is the law commission, use the new logo
if ($activeAgency['shortcode'] === 'law-commission') {
    $logo = get_stylesheet_directory_uri() . '/dist/images/lawcomms_logo_new.png';
}

# If a header logo is set, use that instead
if (!empty($header_logo)) {
    $logo = $header_logo;
}

?>

<section class="c-logo-bar">
  <div class="u-wrapper">
        <div class="u-wrapper__stack--left">
            <a href="/" rel="home">
            <img class="c-logo-bar__logo" aria-hidden="true" src="<?= esc_url($logo) ?>" alt="" />
            <!--  We hide the full header if the user hasn't selected an agency  -->
            <!--  Default to 'Ministry of Justice' in the logo bar in this case  -->
            <span class="agency-title l-half-section"><?= $simpleHeader ? 'Ministry of Justice' : $activeAgency['label'] ?></span>
            </a>
        </div>

        <div class="u-wrapper__stack--right">
            <?php if (get_query_var('name') !== 'agency-switcher') : ?>
            <a href="/agency-switcher" class="c-logo-bar__switch"><?= $simpleHeader ? 'Choose an agency' : 'Switch to other intranet' ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>
