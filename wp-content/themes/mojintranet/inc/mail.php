<?php
// Mail Functions

add_filter('wp_mail_from', 'dw_mail_from');
function dw_mail_from($old) {
  return 'newintranet@digital.justice.gov.uk';
}

add_filter('wp_mail_from_name', 'dw_mail_from_name');
function dw_mail_from_name($old) {
  return 'Intranet';
}

//remove sitename from email subject
add_filter('wp_mail', 'email_subject_remove_sitename');
function email_subject_remove_sitename($email) {
  $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
  $email['subject'] = str_replace("[".$blogname."] - ", "", $email['subject']);
  $email['subject'] = str_replace("[".$blogname."]", "", $email['subject']);
  return $email;
}