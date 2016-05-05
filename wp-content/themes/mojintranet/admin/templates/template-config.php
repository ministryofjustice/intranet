<?php

// Defines template editor options
// Template name doesn't need 'page-' prefix or '.php' suffix
$template_options = array(
  'guidance-and-support' => array(
    'add'       => array(),
    'del'       => array('editor'),
    'js'        => array(),
    'css'       => array(),
    'metaboxes' => array(
      array(
        'id' => 'left_hand_nav',
        'title' => 'Left Hand Navigation',
        'context' => 'side',
        'priority' => 'core',
      ),
    )
  ),
  'single-webchat' => array(
    'add'       => array(),
    'del'       => array(),
    'js'        => array(),
    'css'       => array(),
    'metaboxes' => array(
      array(
        'id'          => 'coveritlive_id',
        'title'       => 'CoveritLive Chat ID',
        'description' => 'Test',
        'context'     => 'normal',
        'priority'    => 'core'
      )
    )
  ),
  'single-event' => array(
    'add'       => array(),
    'del'       => array(),
    'js'        => array('jquery','jquery-ui-datepicker','jquery.plugin','jquery.timeentry'),
    'css'       => array('wp-jquery-ui-dialog','jquery-admin-ui-css','jquery.timeentry'),
    'metaboxes' => array(
      array(
        'id'       => 'event_details',
        'title'    => 'Event Details',
        'context'  => 'normal',
        'priority' => 'core'
      )
    )
  )
);
