<?php

// Added to extend allowed file types in Media upload
add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ( $existing_mimes=array() ) {
  // Add *.RDP files to Media upload
    $existing_mimes['rdp'] = 'application/rdp';
    $existing_mimes['dot'] = 'application/msword';
    $existing_mimes['pot'] = 'application/pot';
    $existing_mimes['xlsb'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    $existing_mimes['xltx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
    $existing_mimes['xla|xls|xlt|xlw'] = 'application/vnd.ms-office';
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

//Disable the real mime check to avoid conflicts with mimetypes reported by PHP and extension
function moj_disable_real_mime_check( $data, $file, $filename, $mimes ) {
    $wp_filetype = wp_check_filetype( $filename, $mimes );

    $ext = $wp_filetype['ext'];
    $type = $wp_filetype['type'];
    $proper_filename = $data['proper_filename'];

    return compact( 'ext', 'type', 'proper_filename' );
}

add_filter( 'wp_check_filetype_and_ext', 'moj_disable_real_mime_check', 10, 4 );
