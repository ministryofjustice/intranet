<?php

use MOJ\Intranet\Multisite;

/**
 * Adjustments to list tables.
 */

$list_tables = array(
    // filename => Class_Name
    'agency-posts' => 'Agency_Posts',
);

// Conditionally add agency column to the users list table if the agency taxonomy is enabled
if(Multisite::isAgencyTaxonomyEnabled()) {
    $list_tables['users'] = 'Users';
}

require_once 'list-tables/list-table.php';

foreach ($list_tables as $include_file => $class_name) {
    require_once 'list-tables/' . $include_file . '.php';
    $class = '\\MOJ_Intranet\\List_Tables\\' . $class_name;
    new $class();
}
