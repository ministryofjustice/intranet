<?php
use MOJ\Intranet\Agency;

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency() ? $oAgency->getCurrentAgency(): 'hq';

get_template_part('src/components/c-global-header/view', $activeAgency['shortcode']);
