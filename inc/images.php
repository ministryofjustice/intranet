<?php

// Image sizes (aspect ratio is 3:2)
add_image_size( "intranet-large", 650, 433, true );
add_image_size( "intranet-small", 280, 182, true );

// Force minimum image dimensions (if not admin)
add_action( 'admin_init', 'dw_force_image_dimensions' );

function dw_force_image_dimensions() {
  // if( !current_user_can( 'administrator') ) {
    add_filter( 'wp_handle_upload_prefilter', 'dw_block_small_images_upload' );
  // }
}

function dw_block_small_images_upload( $file ) {
  // Mime type with dimensions, check to exit earlier
  $mimes = array( 'image/jpeg', 'image/png', 'image/gif' );

  if( !in_array( $file['type'], $mimes ) ) {
    return $file;
  }

  $img = getimagesize( $file['tmp_name'] );
  $minimum = array( 'width' => 960, 'height' => 640 );

  if ( $img[0] < $minimum['width'] ) {
    $file['error'] = 'Image too small. Minimum width is ' . $minimum['width'] . 'px. Uploaded image width is ' . $img[0] . 'px';
  } elseif ( $img[1] < $minimum['height'] ) {
    $file['error'] = 'Image too small. Minimum height is ' . $minimum['height'] . 'px. Uploaded image height is ' . $img[1] . 'px';
  }

  return $file;
}