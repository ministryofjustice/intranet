<?php
/*
Description: Bulk import a directory of files into WP Document Revisions
Version: 0.2
Author: Benjamin J. Balter/Ryan Jarrett
Author URI: http://sparkdevelopment.co.uk
License: GPL2

** NOT A PLUGIN **
This file expects to be in the root of the WordPress folder

To prevent accidental execution, you need to run this file with the query param unlock=unlock

*/

// Set error reporting
error_reporting(E_ALL);

// (Very) basic execution protection
$unlock = $_GET['unlock'];
if($unlock!="unlock") {
  echo "<strong>DO NOT RUN THIS SCRIPT UNLESS YOU KNOW WHAT YOU ARE DOING!!!</strong>";
  die;
}

//relative or absolute path of directory to parse for files
$import_directory = 'wp-content/import/';
//type of file to import
$extension = array('pdf','doc','xls','ppt','jpg','gif','pps');
//initial revision log message (optional)
$revision_message = '';
//id of author to associate with documents, must be valid
$author = '2';
//Initial workflow state ID (optional)
$workflow_state = false;
// Batch size - impose batch size to prevent 502 error on WPEngine
$batch_size = 150;

//Helper function to parse directory for files
function wpdr_get_files( $directory, $extension ) {

  $pattern = "/.*/";

  $dir = new RecursiveDirectoryIterator($directory);
  $ite = new RecursiveIteratorIterator($dir);
  $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
  $fileList = array();
  foreach($files as $file) {
    // Ensure current folder isn't added as a file
    if (basename($file[0])!=".") {
      $fileList = array_merge($fileList, $file);
    }
  }
  return $fileList;

}

//bootstrap WP
require_once ( ABSPATH . 'wp-load.php' );
require ( ABSPATH . 'wp-admin/includes/image.php' );
require ( ABSPATH . 'wp-admin/includes/media.php' );
require ( ABSPATH . 'wp-admin/includes/file.php' );

//array of files, here, a directory dump
$files = wpdr_get_files( $import_directory, $extension );

// total number of files to process
$total_files = sizeof($files);

//add periods to each element in extension array
foreach ($extension as &$value) {
  $value = "." . $value;
}

// Get current slice of files
$files = array_slice($files,0,$batch_size);

//loop through
$file_count = 0;
foreach ( $files as $file ) {

  $file_count++;

  //cleanup filename to title
  $post_name = str_replace(array('-','_'), ' ', basename( $file ) );
  $post_name = str_replace($extension, '', $post_name);
  $post_name = ucwords( $post_name );

  // build post array and insert post
  $post = array(  'post_title' => $post_name,
          'post_status' => 'publish',
          'post_author' => $author,
          'post_content' => '',
          'post_excerpt' => $revision_message,
          'post_type' => 'document',
        );
  $postID = wp_insert_post( $post );

  //if initial workflow state is set, set it
  if ( $workflow_state )
    wp_set_post_terms( $postID, array( $workflow_state ), 'workflow_state' );

  //build attachment array and insert
  $wp_filetype = wp_check_filetype(basename($file), null );

  $attachment = array(
      'post_mime_type' => $wp_filetype['type'],
      'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $file ) ),
      'post_content' => '',
      'post_status' => 'inherit'
   );

  $file_array['name'] = basename($file);
  $file_array['tmp_name'] = $file;

  $id = media_handle_sideload( $file_array, $postID, $post_name );

  //store attachment ID as post content
  $post = array( 'ID' => $postID, 'post_content' => $id);
  wp_update_post( $post );

  //debug info
  echo "<p>$file added as $post_name</p>";

}

echo "<p><b>$batch_size files added</b></p>";