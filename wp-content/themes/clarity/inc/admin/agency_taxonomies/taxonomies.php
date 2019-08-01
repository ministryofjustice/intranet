<?php

/**
 * Instantiate and register Taxonomy objects.
 */

$taxonomies = array(
    'agency' => array("dir" => false, "class-name" => 'Agency'),
    'news-category' => array("dir" => false, "class-name" => 'News_Category'),
    'resource-category' => array("dir" => false, "class-name" => 'Resource_Category'),
    'campaign-category' => array("dir" => false, "class-name" => 'Campaign_Category'),
    'region' => array("dir" => false, "class-name" => 'Region'),
);

require_once 'taxonomies/taxonomy.php';
require_once 'taxonomies/agency-taxonomy.php';
require_once 'taxonomies/content-category.php';
require_once 'taxonomies/agency-terms-walker.php';

foreach ($taxonomies as $include_file => $tax) {
    $include_path = $include_file . '.php' ;
    if ($tax['dir']) {
        $include_path = $tax['dir'] . '/' . $include_path;
    }
    require_once 'taxonomies/' . $include_path;
    $class = '\\MOJ_Intranet\\Taxonomies\\' . $tax['class-name'];
    new $class();
}

/**
 * Remove Default Tags and Categories Taxonomies.
 * Filter: init
 */
function dw_unregister_default_taxonomies(){
    register_taxonomy('post_tag', array());
    register_taxonomy('category', array());
}
add_action('init', 'dw_unregister_default_taxonomies');
