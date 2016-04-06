<?php

class Follow_us_model extends MVC_model {
  function get_data($agency = 'hq') {
    $links = array(
      array(
        'url' => 'https://twitter.com/MoJGovUK',
        'label' => 'MoJ on Twitter',
        'name' => 'twitter',
        'agency' => ' hq hmcts opg laa '
      ),
      array(
        'url' => 'https://www.yammer.com/justice.gsi.gov.uk/dialog/authenticate',
        'label' => 'MoJ on Yammer',
        'name' => 'yammer',
        'agency' => ' hq hmcts opg laa '
      )
    );

    $filtered_links = array();

    foreach($links as $link) {
      if(strpos($link['agency'], ' ' . $agency . ' ') !== false) {
        $filtered_links[] = $link;
      }
    }

    return array(
      'results' => $filtered_links
    );
  }
}
