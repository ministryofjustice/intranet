<?php

class Follow_us_model extends MVC_model {
  function get_data($options) {
    $agency = $options['agency'] ?: 'hq';
    $additional_filters = $options['additional_filters'] ?: '';

    $links = array(
      array(
        'url' => 'https://twitter.com/MoJGovUK',
        'label' => 'MoJ on Twitter',
        'name' => 'twitter',
        'agency' => array('hq', 'hmcts', 'opg', 'laa')
      ),
      array(
        'url' => 'https://www.yammer.com/justice.gsi.gov.uk/dialog/authenticate',
        'label' => 'MoJ on Yammer',
        'name' => 'yammer',
        'agency' => array('hq', 'hmcts', 'opg', 'laa')
      )
    );

    $filtered_links = array();

    foreach($links as $link) {
      if(in_array($agency, $link['agency'])) {
        $filtered_links[] = $link;
      }
    }

    return array(
      'results' => $filtered_links
    );
  }
}
