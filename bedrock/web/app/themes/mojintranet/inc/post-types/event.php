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
        'slug'       => 'events',
        'with_front' => false
      ),
      'hierarchical'  => false,
      'capability_type' => array('event', 'events')
    )
  );

}
add_action( 'init', 'define_event_post_type');

// Add extra columns to event list view in admin
add_filter('manage_event_posts_columns','set_custom_event_columns');
function set_custom_event_columns($columns) {
  $columns['event-start'] = __('Start');
  $columns['event-end'] = __('End');
  $columns['event-allday'] = __('All day event?');
  return $columns;
}

add_action('manage_event_posts_custom_column','custom_event_columns',10,2);
function custom_event_columns($column, $post_id) {
  switch($column) {
    case 'event-start':
      $start_date = get_post_meta($post_id , '_event-start-date' , true);
      $start_time = get_post_meta($post_id , '_event-start-time' , true);
      echo $start_date?$start_date . " " . ($start_time?:"--:--"):"-";
      break;
    case 'event-end':
      $end_date = get_post_meta($post_id, '_event-end-date' , true);
      $end_time = get_post_meta($post_id, '_event-end-time' , true);
      echo $end_date?$end_date . " " . ($end_time?:"--:--"):"-";
      break;
    case 'event-allday':
      echo get_post_meta($post_id, '_event-allday' , true) == true ? 'Yes':'No';
      break;
  }
}

add_filter('manage_edit-event_sortable_columns','sortable_event_columns');
function sortable_event_columns($sortable_columns) {
  $sortable_columns['event-start'] = 'event-start';
  $sortable_columns['event-end'] = 'event-end';
  return $sortable_columns;
}
add_filter('request','mojintranet_sort_events');
function mojintranet_sort_events($vars) {
  if(!is_admin()) {
    return $vars;
  }
  if ( isset( $vars['orderby'] ) && 'event-start' == $vars['orderby'] ) {
    $vars = array_merge( $vars, array(
      'orderby' => 'meta_value',
      'meta_query' => array(
        array(
          'key' => '_event-start-date',
          'compare' => 'exists'
        ),
        array(
          'key' => '_event-start-time',
          'compare' => 'exists'
        )
      )
    ));
  }
  if ( isset( $vars['orderby'] ) && 'event-end' == $vars['orderby'] ) {
    $vars = array_merge( $vars, array(
      'orderby' => 'meta_value',
      'meta_query' => array(
        array(
          'key' => '_event-end-date',
          'compare' => 'exists'
        ),
        array(
          'key' => '_event-end-time',
          'compare' => 'exists'
        )
      )
    ));
  }

  return $vars;
}
