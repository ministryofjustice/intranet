<?php
use MOJ\Intranet\Agency;

if (!defined('ABSPATH')) {
    die();
}

/***
*
 * Finds the current agency that has been set by an agency cookie.
 * Some agency landing pages are currently faux dummy pages that
 * have the agency title but use all of MoJ HQ content. The if statement
 * below assigns the dummy agencies to use the HQ content.
 * As the dummy agencies get onboarded, they can be removed from here.
 *
 */
function get_intranet_code()
{
    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();
    $agency = $activeAgency['shortcode'];

    if ($agency === 'noms' || $agency === 'ospt' || $agency === 'judicial-appointments-commission') {
        $agency = 'hq';
        return $agency;
    } else {
        return $agency;
    }
}
