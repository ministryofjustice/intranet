<?php

function alter_customiser_info_text($translated, $original) {
  if($original == 'The Customizer allows you to preview changes to your site before publishing them. You can also navigate to different pages on your site to preview them.') {
    return 'The preview pane on this page won’t display any changes you make. To view your changes, wait 60 seconds after saving the page and then view the homepage on the live site. If the live page hasn’t updated after 60 seconds, contact newintranet@digital.justice.gov.uk.';
  }
  return $translated;
}

add_filter('gettext', 'alter_customiser_info_text', 10, 3);


