<?php

// Author info functions

/**
 * Return author information consistently
 * @param  int $post_id Post ID
 * @return array          Array of authors (and related information)
 */
function dw_get_author_info($post_id) {
  if(function_exists('get_coauthors')) {
    $authors_array = get_coauthors($post_id);
    $authors = null;
    foreach($authors_array as $author) {
      $author_id = (int) $author->ID;

      if($author->data) {
        $author_name = $author->data->display_name;
        $author_thumb = get_avatar_url($author_id);
      } else {
        $author_name = $author->display_name;
        $author_thumb_id = get_post_thumbnail_id($author_id);
        $author_thumb = wp_get_attachment_image_src($author_thumb_id, 'user-thumb')[0];
      }

      $authors[] = array(
        // 'all_data' => $author,
        'id'            => $author_id,
        'name'          => $author_name,
        'thumbnail_url' => $author_thumb,
        'job_title'     => '',
        'bio'           => ''
      );
    }
  } else {
    $authors = array(
      'id'            => get_the_author_meta('ID',$post->ID),
      'name'          => get_the_author_meta('display_name',$post->ID),
      'thumbnail_url' => get_avatar_url(get_the_author_meta('ID',$post->ID))
    );
  }

  return $authors;
}

/**
 * Add additional fields to author profile
 * @param array $fields_to_return Author profile fields
 * @param array $groups           Field groups
 */
function dw_add_author_fields( $fields_to_return, $groups ) {
	if ( in_array( 'all', $groups ) || in_array( 'contact-info', $groups ) ) {
	   $fields_to_return[] = array(
       'key'      => 'job_title',
       'label'    => 'Job Title',
       'group'    => 'contact-info',
     );
     foreach ($fields_to_return as $index=>$field) {
       $fields_to_delete=array('yim','aim','jabber','yahooim','website');
       if(in_array($field['key'],$fields_to_delete)) {
         unset($fields_to_return[$index]);
       }
     }
	}
	return $fields_to_return;
}
add_filter( 'coauthors_guest_author_fields', 'dw_add_author_fields', 10, 2 );

/**
 * Remove unecessary contact methods from user profile
 * @param  array $contactmethods Current contact methods
 * @return array                 Updated contact methods
 */
function dw_edit_contactmethods( $contactmethods ) {
  $fields_to_delete=array('yim','aim','jabber','yahooim','website');
  foreach($fields_to_delete as $field) {
    unset($contactmethods[$field]);
  }
  return $contactmethods;
}
add_filter('user_contactmethods','dw_edit_contactmethods',10,1);

?>
