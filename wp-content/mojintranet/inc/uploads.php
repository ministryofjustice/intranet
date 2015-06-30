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