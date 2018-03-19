<?php
namespace MOJ\Intranet;

/**
 * Return author information consistently and manage author data
 *
 *
**/

class Authors {
    function getAuthorInfo($post_id) {
      $authors = array();

      //Function from plugin Co-Authors-Plus
      if (function_exists('get_coauthors')) {
        $authors_array = get_coauthors($post_id);
        foreach ($authors_array as $author) {
          $author_id = $author->ID;

          if (property_exists($author, 'data') && $author->data) {
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
}
