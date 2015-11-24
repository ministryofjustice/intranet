<?php

// Author info functions

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
        'thumbnail_url' => $author_thumb
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

?>
