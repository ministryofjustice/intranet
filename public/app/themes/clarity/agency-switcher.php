<?php

use MOJ\Intranet\Agency;

/*
*  Agency switcher
*/
get_header();

$oAgency = new Agency();
$simpleHeader = !$oAgency->hasAgencyCookie();

$heading = 'Choose the version of the intranet you want to see';
$body = null;
// If we're using the simple header, assume a new user and change the intro text to reflect this
if ($simpleHeader) {
    $heading = 'Welcome to the Ministry of Justice intranet';
    $body = '<p>Choose the version you want to see.</p><p>You can change this at any time using the "Switch to other 
                intranet" link at the top of any page.</p>';
}
?>
    <main id="maincontent" class="u-wrapper l-main t-agency-switcher" role="main">
        <h1 class="o-title o-title--page">
            <?= $heading ?>
        </h1>
        <?= $body ? "<p>$body</p>" : '' ?>
        <?php get_template_part('src/components/c-intranet-switcher/view'); ?>
    </main>
<?php
get_footer();
