<?php

use MOJ\Intranet\Agency;

/*
*  Agency switcher
*/
get_header();

$oAgency = new Agency();
$simpleHeader = !$oAgency->hasAgencyCookie();

$heading = 'Choose your agency or body';
$body = 'Other agencies and bodies have their own specific intranet content available to view by visiting the links 
below. HMPPS and YJB links are external intranet websites not managed by this central MoJ intranet.';

// If we're using the simple header, assume a new user and change the intro text to reflect this
if ($simpleHeader) {
    $heading = 'Welcome to the Ministry of Justice intranet';
    $body = 'Please choose your agency or body to access the intranet content specific to your organisation.';
}
?>
    <main id="maincontent" class="u-wrapper l-main t-agency-switcher" role="main">
        <h1 class="o-title o-title--page">
            <?= $heading ?>
        </h1>
        <p>
            <?= $body ?>
        </p>
        <?php get_template_part('src/components/c-intranet-switcher/view'); ?>
    </main>
<?php
get_footer();
