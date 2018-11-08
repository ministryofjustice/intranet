<?php

/**
 * The standard WordPress "post" Post Type is called Blogs in our theme
 * Below is what modifies it to display as blogs
 */

// We don't want blogs to show up in the subscriber

if ( ! current_user_can( 'subscriber') ) :

  add_action('admin_menu', 'change_post_menu_label');

  function change_post_menu_label()
  {
      global $menu;
      global $submenu;

      $menu[5][0]                 = 'Blogs';
      $submenu['edit.php'][5][0]  = 'Blogs';
      $submenu['edit.php'][10][0] = 'Add Blogs';
      echo '';
  }

  add_action('init', 'change_post_object_label');

  function change_post_object_label()
  {
      global $wp_post_types;

      $labels                     = &$wp_post_types['post']->labels;
      $labels->name               = 'Blogs';
      $labels->singular_name      = 'Blog';
      $labels->add_new            = 'Add Blog';
      $labels->add_new_item       = 'Add Blog';
      $labels->edit_item          = 'Edit Blogs';
      $labels->new_item           = 'Blog';
      $labels->view_item          = 'View Blog';
      $labels->search_items       = 'Search Blogs';
      $labels->not_found          = 'No Blogs found';
      $labels->menu_position      = '2';
      $labels->not_found_in_trash = 'No Blogs found in Trash';
  }

endif;
