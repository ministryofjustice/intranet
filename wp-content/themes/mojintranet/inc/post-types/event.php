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
      'supports'      => array('title','editor','thumbnail','excerpt','page-attributes'),
      'has_archive'   => false,
      'rewrite'       => array(
        'slug'       => 'events'
      ),
      'hierarchical'  => false
    )
  );

}
add_action( 'init', 'define_event_post_type');
