<?php

// Author info functions

/**
 * Return author information consistently
 * @param  int $post_id Post ID
 * @return array          Array of authors (and related information)
 */
function dw_get_author_info($post_id) {
  $authors = array();

  if (function_exists('get_coauthors')) {
    $authors_array = get_coauthors($post_id);
    foreach ($authors_array as $author) {
      $author_id = $author->ID;

      if (method_exists($author, 'data') && $author->data) {
        $author_name = $author->data->display_name;
        $author_thumb = get_avatar_url($author_id);
        $author_job_title = get_the_author_meta('user_job_title', $author_id);
        $author_bio = get_the_author_meta('description', $author_id);
        $author_alt_text = '';
      }
      else {
        $author_name = $author->display_name;
        $author_thumb_id = get_post_thumbnail_id($author_id);
        $author_thumb = wp_get_attachment_image_src($author_thumb_id, 'user-thumb')[0];
        $author_alt_text = get_post_meta($author_thumb_id, '_wp_attachment_image_alt', true);
        $author_job_title = $author->job_title;
        $author_bio = $author->description;
      }

      $authors[] = array(
        // 'all_data' => $author,
        'id'            => (int) $author_id,
        'name'          => $author_name,
        'thumbnail_url' => $author_thumb,
        'thumbnail_alt_text' => $author_alt_text,
        'job_title'     => $author_job_title,
        'bio'           => $author_bio
      );
    }
  }
  else {
    $user_id = get_post_field('post_author', $post_id);
    $avatar_url = get_avatar_url(get_the_author_meta('ID', $user_id));
    $author_thumb_id = get_attachment_id_from_url($avatar_url);
    $author_alt_text = get_post_meta($author_thumb_id, '_wp_attachment_image_alt', true) ?: "";
    $authors[] = array(
      'id'            => (int) $user_id,
      'name'          => get_the_author_meta('display_name', $user_id),
      'thumbnail_url' => $avatar_url,
      'thumbnail_alt_text' => $author_alt_text,
      'job_title'     => '',
      'bio'           => ''
    );
  }

  return $authors;
}

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
