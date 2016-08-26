<?php
/**
 * Remove Categories and Tags columns from the post listing page.
 * Filter: manage_posts_columns
 *
 * @param array $columns
 * @return array
 */
function dw_edit_posts_columns($columns) {
  unset($columns['categories']);
  unset($columns['tags']);
  return $columns;
}
add_filter('manage_posts_columns' , 'dw_edit_posts_columns');

/**
 * Remove Comments column from the pages listing page.
 * Filter: manage_pages_columns
 *
 * @param array $columns
 * @return array
 */
function dw_edit_page_columns($columns) {
  unset($columns['comments']);
  return $columns;
}
add_filter('manage_pages_columns' , 'dw_edit_page_columns');
