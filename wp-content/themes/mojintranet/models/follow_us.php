<?php

class Follow_us_model extends MVC_model {
  function get_data($options) {
    $agency = $options['agency'] ?: 'hq';

    $links = [
      'ppo' => [
      ],
      'judicial-office' => [
      ],
      'cica' => [
      ],
      'pb' => [
      ],
      'hq' => [
        [
          'url' => 'https://twitter.com/MoJGovUK',
          'label' => 'MoJ on Twitter',
          'name' => 'twitter',
        ],
        [
          'url' => 'https://www.yammer.com/justice.gsi.gov.uk/dialog/authenticate',
          'label' => 'MoJ on Yammer',
          'name' => 'yammer',
        ]
      ],
      'hmcts' => [
        [
          'url' => 'https://twitter.com/MoJGovUK',
          'label' => 'MoJ on Twitter',
          'name' => 'twitter',
        ],
        [
          'url' => 'https://www.yammer.com/hmcts.gsi.gov.uk',
          'label' => 'HMCTS on Yammer',
          'name' => 'yammer',
        ]
      ],
      'laa' => [
        [
          'url' => 'https://twitter.com/legalaidagency',
          'label' => 'LAA on Twitter',
          'name' => 'twitter',
        ],
        [
          'url' => 'https://www.yammer.com/legalaid.gsi.gov.uk/',
          'label' => 'LAA on Yammer',
          'name' => 'yammer',
        ]
      ],
      'opg' => [
        [
          'url' => 'https://twitter.com/opggovuk',
          'label' => 'OPG on Twitter',
          'name' => 'twitter',
        ]
      ]
    ];


    return array(
        'results' => $links[$agency]
    );
  }
}
