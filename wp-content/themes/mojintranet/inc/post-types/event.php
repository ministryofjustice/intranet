<?php
// Define event post type
function define_event_post_type() {
  global $wp_post_types;
  register_post_type( 'event',
    array(
      'labels'        => array(
        'name'               => __('Events'),
        'singular_name'      => __('Event'),
        'add_new_item'       => __('Add New Event'),
        'edit_item'          => __('Edit Event'),
        'new_item'           => __('New Event'),
        'view_item'          => __('View Event'),
        'search_items'       => __('Search Events'),
        'not_found'          => __('No events found'),
        'not_found_in_trash' => __('No events found in Trash')
      ),
      'description'   => __('Contains details of events'),
      'public'        => true,
      'menu_position' => 20,
      'menu_icon'     => 'dashicons-calendar-alt',
      'supports'      => array('title','editor','thumbnail','page-attributes'),
      'has_archive'   => false,
      'rewrite'       => array(
        'slug'       => 'events'
      ),
      'hierarchical'  => false
    )
  );

}
add_action( 'init', 'define_event_post_type');

// Add extra columns to event list view in admin
add_filter('manage_event_posts_columns','set_custom_event_columns');
function set_custom_event_columns($columns) {
  $columns['event-start-date'] = __('Start Date');
  $columns['event-start-time'] = __('Start Time');
  $columns['event-end-date'] = __('End Date');
  $columns['event-end-time'] = __('End Time');
  return $columns;
}

add_action('manage_event_posts_custom_column','custom_event_columns',10,2);
function custom_event_columns($column, $post_id) {
  switch($column) {
    case 'event-start-date':
      echo get_post_meta( $post_id , '_event-start-date' , true )?:'-';
      break;
    case 'event-start-time':
      echo get_post_meta( $post_id , '_event-start-time' , true )?:'-';
      break;
    case 'event-end-date':
      echo get_post_meta( $post_id , '_event-end-date' , true )?:'-';
      break;
    case 'event-end-time':
      echo get_post_meta( $post_id , '_event-end-time' , true )?:'-';
      break;
  }
}
