<?php

use MOJ\Intranet;

function remove_regions_from_nonhmcts_users(){
  $context = Agency_Context::get_agency_context();

  if($context != 'hmcts')
  {
    remove_menu_page('edit.php?post_type=regional_news');
    remove_menu_page('edit.php?post_type=regional_page');
  }
}
add_action('admin_menu', 'remove_regions_from_nonhmcts_users');

function remove_options_from_agency_admin ()
{
  //creating functions post_remove for removing menu item
  $current_user = wp_get_current_user();
  $get_role = $current_user->roles[0];
  if($get_role == 'agency_admin')
  {
    remove_menu_page('edit.php?post_type=acf-field-group');
    remove_menu_page('options-general.php');
  }

}
add_action('admin_menu', 'remove_options_from_agency_admin');

function remove_options_from_teamusers ()
{
  //creating functions post_remove for removing menu item
  $current_user = wp_get_current_user();
  $get_role = $current_user->roles[0];
  if($get_role == 'team-author' || $get_role == 'team-lead')
  {
    remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit.php?post_type=acf-field-group' );
    remove_menu_page( 'edit.php?post_type=webchat' );
    remove_menu_page( 'acf-options' );
    remove_menu_page( 'options-general.php' );
  }

}
add_action('admin_menu', 'remove_options_from_teamusers');

function remove_options_from_regionalusers (){
  //creating functions post_remove for removing menu item
  $current_user = wp_get_current_user();
  $get_role = $current_user->roles[0];
  if($get_role == 'regional-editor')
  {
    remove_menu_page( 'edit.php' );
  }
}
add_action('admin_menu', 'remove_options_from_regionalusers');
