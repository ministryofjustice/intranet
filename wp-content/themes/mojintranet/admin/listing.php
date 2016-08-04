<?php
function dw_edit_posts_columns($columns) {
  unset($columns['categories']);
  unset($columns['tags']);
  return $columns;
}
add_filter('manage_posts_columns' , 'dw_edit_posts_columns');

function dw_edit_page_columns($columns) {
  unset($columns['comments']);
  return $columns;
}
add_filter('manage_pages_columns' , 'dw_edit_page_columns');
