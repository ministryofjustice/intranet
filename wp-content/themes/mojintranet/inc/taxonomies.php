<?php

/**
 * Instantiate and register Taxonomy objects.
 */

$taxonomies = array(
    // filename => Class_Name
    'agency' => 'Agency',
    'news-category' => 'News_Category',
    'resource-category' => 'Resource_Category',
);

require_once 'taxonomies/taxonomy.php';
require_once 'taxonomies/content-category.php';
require_once 'taxonomies/agency-terms-walker.php';

foreach ($taxonomies as $include_file => $class_name) {
    require_once 'taxonomies/' . $include_file . '.php';
    $class = '\\MOJ_Intranet\\Taxonomies\\' . $class_name;
    new $class();
}
