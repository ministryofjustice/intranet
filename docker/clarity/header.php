<?php
use MOJ\Intranet\Agency;

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
/**
* Setting no-cache below makes sure the set cookie stays set,
* when users are moving back and forward from the old site to the new, via the main menu.
*
*/
header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Pragma: no-cache');
/**
* This gets the component view.php in c-global-header.
* If you're visiting the page with an agency cookie set, it will get the specific agency php in
* that c-global-header, so for example /view-laa.php
*
*/
get_template_part('src/components/c-global-header/view', $activeAgency['shortcode']);
