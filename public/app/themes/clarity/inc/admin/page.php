<?php

// Page post type. Add excerpts to pages
add_action('init', 'add_page_excerpts');

function add_page_excerpts()
{
    add_post_type_support('page', 'excerpt');
}
