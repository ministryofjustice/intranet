<?php

// Adds additional columns to wp-admin tables

// Add news type columns to news stories
function add_news_columns( $columns ) {
  return array_merge($columns, 
              array('news_type' => __('News Type')));
}
add_filter( 'manage_news_posts_columns', 'add_news_columns', 10, 2);

function custom_news_type_column( $column, $post_id ) {
    switch ( $column ) {
      case 'news_type':
        echo pods_field_display('news',$post_id, 'news_listing_type.name');
        break;
    }
}
add_action( 'manage_news_posts_custom_column' , 'custom_news_type_column', 10, 2 );