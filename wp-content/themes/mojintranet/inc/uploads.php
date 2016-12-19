<?php

// Added to extend allowed file types in Media upload
add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ( $existing_mimes=array() ) {
  // Add *.RDP files to Media upload
  $existing_mimes['rdp'] = 'application/rdp';
  $existing_mimes['dot'] = 'application/dot';
  $existing_mimes['pot'] = 'application/pot';
  return $existing_mimes;
}

// Set documents uploaded via WP Document Revisions to be public by default
add_filter( 'document_to_private', 'dont_make_private', 10, 2);
function dont_make_private($post, $post_pre ){
	return $post_pre;
}

add_filter('media_row_actions','hide_media_view_link', 10, 2);
function hide_media_view_link($actions, $post){
    unset($actions['view']);
    return $actions;
}
