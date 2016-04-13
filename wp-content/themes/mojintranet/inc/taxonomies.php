<?php

/**
 * Instantiate and register Taxonomy objects.
 */

$taxonomies = array(
    // filename => ClassName
    'agency' => 'Agency',
);

require_once 'taxonomies/taxonomy.php';

foreach ($taxonomies as $includeFile => $className) {
    require_once 'taxonomies/' . $includeFile . '.php';
    $class = '\\MOJIntranet\\Taxonomies\\' . $className;
    new $class();
}
