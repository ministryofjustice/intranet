<?php
/**
 * Add additional fields to author profile
 * @param array $fields_to_return Author profile fields
 * @param array $groups           Field groups
 */
function dw_add_author_fields($fields_to_return, $groups) {
	if (in_array('all', $groups) || in_array('contact-info', $groups)) {
	   $fields_to_return[] = [
         'key'      => 'job_title',
         'label'    => 'Job Title',
         'group'    => 'contact-info',
       ];

       foreach ($fields_to_return as $index=>$field) {
         $fields_to_delete = ['yim','aim','jabber','yahooim','website'];

         if (in_array($field['key'], $fields_to_delete)) {
           unset($fields_to_return[$index]);
         }
       }
	}
	return $fields_to_return;
}
add_filter('coauthors_guest_author_fields', 'dw_add_author_fields', 10, 2);

/**
 * Remove unecessary contact methods from user profile
 * @param  array $contactmethods Current contact methods
 * @return array                 Updated contact methods
 */
function dw_edit_contactmethods($contactmethods) {
  $fields_to_delete = ['yim','aim','jabber','yahooim','website'];

  foreach ($fields_to_delete as $field) {
    unset($contactmethods[$field]);
  }
  return $contactmethods;
}
add_filter('user_contactmethods', 'dw_edit_contactmethods', 10, 1);

/**
 * Allow editors to manage guest author profiles
 */
function dw_filter_guest_author_manage_cap($cap) {
  return 'edit_others_posts';
}
add_filter('coauthors_guest_author_manage_cap', 'dw_filter_guest_author_manage_cap');

/**
 * Checks if a local avatar has been selected by a user
 * @param  string $url Current url of avatar
 * @param  string $url ID or Email of user
 * @param  array $args Attributes of the avatar
 * @return string Url of avatar
 */
function check_local_avatar($url, $id_or_email, $args) {
  if(is_numeric($id_or_email)) {
    $local_avatar = get_user_meta($id_or_email, 'wp_user_avatar', true);

    if (is_numeric($local_avatar)) {
      $url = wp_get_attachment_image_src($local_avatar, 'user-thumb')[0];
    }
  }
  return $url;
}
add_filter('get_avatar_url', 'check_local_avatar', 99, 3);
