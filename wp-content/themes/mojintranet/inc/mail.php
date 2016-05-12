<?php
// Mail Functions

add_filter('wp_mail_from', 'dw_mail_from');
function dw_mail_from($old) {
  return 'newintranet@digital.justice.gov.uk';
}

add_filter('wp_mail_from_name', 'dw_mail_from_name');
function dw_mail_from_name($old) {
  return 'newintranet';
}
