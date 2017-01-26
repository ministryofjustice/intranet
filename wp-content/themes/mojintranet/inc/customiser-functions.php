<?php

function alter_customiser_info_text($translated, $original) {
  if($original == 'The Customizer allows you to preview changes to your site before publishing them. You can also navigate to different pages on your site to preview them.') {
    return 'Here';
  }
  return $translated;
}

add_filter('gettext', 'alter_customiser_info_text', 10, 3);


