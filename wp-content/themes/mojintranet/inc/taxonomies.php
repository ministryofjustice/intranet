<?php

/**
 * Instantiate and register Taxonomy objects.
 */

$taxonomies = array(
    // filename => Class_Name
    'agency' => 'Agency',
);

require_once 'taxonomies/taxonomy.php';

foreach ($taxonomies as $include_file => $class_name) {
    require_once 'taxonomies/' . $include_file . '.php';
    $class = '\\MOJ_Intranet\\Taxonomies\\' . $class_name;
    new $class();
}
