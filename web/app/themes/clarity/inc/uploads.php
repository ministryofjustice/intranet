<?php

// Added to extend allowed file types in Media upload
add_filter('upload_mimes', 'custom_upload_mimes');

function custom_upload_mimes($existing_mimes = array())
{
    
    $existing_mimes['png'] = 'image/png';
    $existing_mimes['jpeg'] = 'image/jpeg';
    $existing_mimes['jpg'] = 'image/jpeg';
    $existing_mimes['js'] = 'text/javascript';
    $existing_mimes['json'] = 'application/json';
    $existing_mimes['pdf'] = 'application/pdf';
    $existing_mimes['csv'] = 'text/csv';
    $existing_mimes['doc'] = 'application/msword';
    $existing_mimes['dot'] = 'application/msword';
    $existing_mimes['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    $existing_mimes['dotx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';
    $existing_mimes['docm'] = 'application/vnd.ms-word.document.macroEnabled.12';
    $existing_mimes['dotm'] = 'application/vnd.ms-word.template.macroEnabled.12';
    $existing_mimes['xls'] = 'application/vnd.ms-excel';
    $existing_mimes['xlt'] = 'application/vnd.ms-excel';
    $existing_mimes['xla'] = 'application/vnd.ms-excel';
    $existing_mimes['xlw'] = 'application/vnd.ms-office';
    $existing_mimes['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    $existing_mimes['xltx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
    $existing_mimes['xlsm'] = 'application/vnd.ms-excel.sheet.macroEnabled.12';
    $existing_mimes['xltm'] = 'application/vnd.ms-excel.template.macroEnabled.12';
    $existing_mimes['xlam'] = 'application/vnd.ms-excel.addin.macroEnabled.12';
    $existing_mimes['xlsb'] = 'application/vnd.ms-excel.sheet.binary.macroEnabled.12';
    $existing_mimes['ppt'] = 'application/vnd.ms-powerpoint';
    $existing_mimes['pot'] = 'application/vnd.ms-powerpoint';
    $existing_mimes['pps'] = 'application/vnd.ms-powerpoint';
    $existing_mimes['ppa'] = 'application/vnd.ms-powerpoint';
    $existing_mimes['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    $existing_mimes['potx'] = 'application/vnd.openxmlformats-officedocument.presentationml.template';
    $existing_mimes['ppsx'] = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
    $existing_mimes['ppam'] = 'application/vnd.ms-powerpoint.addin.macroEnabled.12';
    $existing_mimes['pptm'] = 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
    $existing_mimes['potm'] = 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
    $existing_mimes['ppsm'] = 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12';
    $existing_mimes['rdp'] = 'application/rdp';

    return $existing_mimes;
}

// Set documents uploaded via WP Document Revisions to be public by default
add_filter('document_to_private', 'dont_make_private', 10, 2);

function dont_make_private($post, $post_pre)
{
    return $post_pre;
}

add_filter('media_row_actions', 'hide_media_view_link', 10, 2);

function hide_media_view_link($actions, $post)
{
    unset($actions['view']);
    return $actions;
}

add_filter('wp_check_filetype_and_ext', 'moj_disable_real_mime_check', 10, 4);

//Disable the real mime check to avoid conflicts with mimetypes reported by PHP and extension
function moj_disable_real_mime_check($data, $file, $filename, $mimes)
{
    $wp_filetype = wp_check_filetype($filename, $mimes);

    $ext = $wp_filetype['ext'];
    $type = $wp_filetype['type'];
    $proper_filename = $data['proper_filename'];

    return compact('ext', 'type', 'proper_filename');
}
