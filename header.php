<?php
/*
* Setting no-cache here to handle users moving from the old site, via the main menu
* to the new site, causing the header to display the wrong agency information due to the
* wrong cookie being loaded.
*/
header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Pragma: no-cache');

get_component('c-global-header');
