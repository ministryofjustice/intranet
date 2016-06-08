<?php

/**
 * Instantiate and register Taxonomy objects.
 */

$taxonomies = array(
    // filename => array("dir" => 'directory', "class-name" => 'Class_Name')
    'agency' => array("dir" => false, "class-name" => 'Agency'),
    //'laa-region' => array("dir" => 'laa', "class-name" => 'LAA_Region'),
    //'hmcts-region' => array("dir" => 'hmcts', "class-name" => 'HMCTS_Region'),
);

require_once 'taxonomies/taxonomy.php';
require_once 'taxonomies/agency-taxonomy.php';

foreach ($taxonomies as $include_file => $tax) {
    $include_path = $include_file . '.php' ;
    if ($tax['dir']) {
        $include_path = $tax['dir'] . '/' . $include_path;
    }
    require_once 'taxonomies/' . $include_path;
    $class = '\\MOJ_Intranet\\Taxonomies\\' . $tax['class-name'];
    new $class();
}
