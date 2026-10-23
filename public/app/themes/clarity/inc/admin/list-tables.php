<?php

/**
 * Adjustments to list tables.
 */

$list_tables = array(
    // filename => Class_Name
    'users' => 'Users',
    'agency-posts' => 'Agency_Posts',
    'date-range' => 'Date_Range',
    'agency-scope' => 'Agency_Scope',
);

require_once 'list-tables/list-table.php';
require_once 'list-tables/listing-query.php';

foreach ($list_tables as $include_file => $class_name) {
    require_once 'list-tables/' . $include_file . '.php';
    $class = '\\MOJ_Intranet\\List_Tables\\' . $class_name;
    new $class();
}
