<?php

// Defines template editor options
// Template name doesn't need 'page-' prefix or '.php' suffix
$template_options = array(
  'guidance-and-support'  =>  array(
    'add' => array(),
    'del' => array('editor'),
    'metaboxes' => array(
      array(
        'id' => 'quick_links',
        'title' => 'Quick Links',
        'context' => 'normal',
        'priority' => 'core'
      ),
      array(
        'id' => 'content_tabs',
        'title' => 'Content Tabs',
        'context' => 'normal',
        'priority' => 'core'
      )
    ),
    'js' => array('jquery','jquery-ui-draggable','jquery-ui-tabs','jquery-ui-accordion','jquery-ui-dialog'),
    'css' => array('wp-jquery-ui-dialog','jquery-admin-ui-css')
  )
);