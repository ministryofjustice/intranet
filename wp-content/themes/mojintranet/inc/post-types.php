<?php
// ----------------------------------------
// Controls post-types (custom and built-in)
// ----------------------------------------

// CPT DEFINITIONS
// ---------------
$post_types_folder = 'post-types';
$post_types_array = array('webchat','event', 'news', 'regional-page', 'regional-news');
foreach ($post_types_array as $post_type) {
    include_once($post_types_folder . '/' . $post_type . '.php');
}
