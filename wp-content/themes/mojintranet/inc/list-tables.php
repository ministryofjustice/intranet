<?php

/**
 * Adjustments to list tables.
 */

$listTables = array(
    // filename => ClassName
    'users' => 'Users',
    'agency-posts' => 'AgencyPosts',
);

require_once 'list-tables/list-table.php';

foreach ($listTables as $includeFile => $className) {
    require_once 'list-tables/' . $includeFile . '.php';
    $class = '\\MOJIntranet\\ListTables\\' . $className;
    new $class();
}
