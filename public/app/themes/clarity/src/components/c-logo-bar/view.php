<?php

use MOJ\Intranet\Agency;
use MOJ\Intranet\Multisite;

$blog_is_single_agency = Multisite::isSingleAgencyBlog();
// If we are on a multisite blog and it only has one agency, then the field prefix is empty.
$field_prefix = '';
// If we are on a multisite blog and it only has one agency, then never use the simple header.
$simpleHeader = false;

// Initialise the Agency class
$oAgency = new Agency();
// This function works for both single and multisite blogs. 
$activeAgency = $oAgency->getCurrentAgency();

if (!$blog_is_single_agency) {
    // Show a simplified header if the user has not yet chosen an agency
    $simpleHeader = !$oAgency->hasAgencyCookie();

    // Set the field prefix to the agency shortcode.
    $field_prefix = get_intranet_code() . '_';
}

/*
 * Logo
 */

// Set the default logo.
$logo = get_stylesheet_directory_uri() . '/dist/images/moj_logo_header.png';

// If the agency is the law commission, use the new logo
if ($activeAgency['shortcode'] === 'law-commission') {
    $logo = get_stylesheet_directory_uri() . '/dist/images/lawcomms_logo_new.png';
}

// Get the header logo from the options page.
$header_logo = get_field($field_prefix  . 'header_logo', 'option');

// If a header logo is set, use that instead
if (!empty($header_logo)) {
    $logo = $header_logo;
}

?>

<section class="c-logo-bar">
    <div class="u-wrapper">
        <div class="u-wrapper__stack--left">
            <a href="/" rel="home">
                <img class="c-logo-bar__logo" aria-hidden="true" src="<?= $logo ?>" alt="" />
                <!--  We hide the full header if the user hasn't selected an agency  -->
                <!--  Default to 'Ministry of Justice' in the logo bar in this case  -->
                <span class="agency-title l-half-section"><?= $simpleHeader ? 'Ministry of Justice' : $activeAgency['label'] ?></span>
            </a>
        </div>

        <div class="u-wrapper__stack--right">
            <?php if (get_query_var('name') !== 'agency-switcher') : ?>
                <a href="<?= get_home_url(1, '/agency-switcher') ?>" class="c-logo-bar__switch"><?= $simpleHeader ? 'Choose an agency' : 'Switch to other intranet' ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>
