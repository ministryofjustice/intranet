<?php
namespace MOJ\Intranet;

if (!defined('ABSPATH')) {
    die();
}

class Agency
{
    public function getContactEmailAdress($agency = 'hq')
    {
        $agency_array = $this->getList();

        if (isset($agency_array[$agency]) && $agency_array[$agency]['is_integrated'] == true && !empty($agency_array[$agency]['contact_email_address'])) {
            return $agency_array[$agency]['contact_email_address'];
        } else {
            return $agency_array['hq']['contact_email_address'];
        }
    }

    public function getList()
    {
        /**
     * Agency array structure:
     *
     *  - shortcode (string) - agency code
     *  - label (string) - the full name of the agency
     *  - abbreviation (string) - short name, such as HMCTS
     *  - blog url (string) (optional) - custom url for main menu blog
     *  - is_integrated (boolean) - whether the agency is already integrated into the intranet or not
     *  - contact_email_address (string) (optional) - the email address used for the feedback form
     *  - links (array) - links that display into the My MoJ section, with fields:
     *      - url (string) - URL of the link
     *      - label (string) - Text label for the link
     *      - classes (string) (optional) - Classes for the HTML element
     *      - is_external (boolean) - Is this a link to an external site?
     */
    return [
      'cica' => [
        'shortcode' => 'cica',
        'label' => 'Criminal Injuries Compensation Authority',
        'abbreviation' => 'CICA',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-cica@digital.justice.gov.uk',
        'links' => [],
        'wp_tag_id' => 1049, //ID number from Taxonomy 'Agency' Term 'CICA'
      ],
      'hmcts' => [
        'shortcode' => 'hmcts',
        'label' => 'HM Courts &amp; Tribunals Service',
        'abbreviation' => 'HMCTS',
        'blog_url' => '/blog/',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-hmcts@digital.justice.gov.uk',
        'wp_tag_id' => 100, //ID number from Taxonomy 'Agency' Term 'HMCTS'
        'links' => [
          [
            'url' => site_url('/about-hmcts/justice-matters/'),
            'label' => 'Justice Matters',
            'classes' => 'transformation',
            'is_external' => true
          ]
        ]
      ],
      'noms' => [
        'shortcode' => 'noms',
        'label' => 'HM Prison & Probation Service',
        'abbreviation' => 'HMPPS',
        'is_integrated' => false,
        'wp_tag_id' => 99, //ID number from Taxonomy 'Agency' Term 'HQ' as there isn;t a HMPPS
        'links' => [
          [
            'url' => 'https://intranet.noms.gsi.gov.uk/',
            'label' => 'HM Prison & Probation Service intranet',
            'is_external' => true
          ]
        ]
      ],
      'judicial-appointments-commission' => [
        'shortcode' => 'judicial-appointments-commission',
        'label' => 'Judicial Appointments Commission',
        'abbreviation' => 'JAC',
        'is_integrated' => false,
        'wp_tag_id' => 99, //ID number from Taxonomy 'Agency' Term 'HMCTS'
        'links' => [
          [
            'url' => 'http://jac.intranet.service.justice.gov.uk/',
            'label' => 'Judicial Appointments Commission intranet',
            'is_external' => true
          ]
        ]
      ],
      'judicial-office' => [
        'shortcode' => 'judicial-office',
        'label' => 'Judicial Office',
        'abbreviation' => 'JO',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-jo@digital.justice.gov.uk',
        'wp_tag_id' => 1165, //ID number from Taxonomy 'Agency' Term 'JO'
        'links' => []
      ],
      'law-commission' => [
        'shortcode' => 'law-commission',
        'label' => 'Law Commission',
        'abbreviation' => 'LawCom',
        'is_integrated' => false,
        'wp_tag_id' => 99, //ID number from Taxonomy 'Agency' Term 'HQ' as there isn;t a LA
        'links' => [
          [
            'url' => 'http://lawcommission.intranet.service.justice.gov.uk/',
            'label' => 'Law Commission intranet',
            'is_external' => true
          ]
        ]
      ],
      'laa' => [
        'shortcode' => 'laa',
        'label' => 'Legal Aid Agency',
        'abbreviation' => 'LAA',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-laa@digital.justice.gov.uk',
        'wp_tag_id' => 101, //ID number from Taxonomy 'Agency' Term 'LAA'
        'links' => []
      ],
      'hq' => [
        'shortcode' => 'hq',
        'label' => 'Ministry of Justice HQ',
        'abbreviation' => 'MoJ',
        'is_integrated' => true,
        'default' => true,
        'contact_email_address' => 'intranet@justice.gsi.gov.uk',
        'wp_tag_id' => 99, //ID number from Taxonomy 'Agency' Term 'HQ'
        'links' => [
          [
            'url' => site_url('/about-us/moj-transformation'),
            'label' => 'MoJ TRANSFORMATION',
            'is_external' => false,
            'classes' => 'transformation'
          ]
        ]
      ],
      'opg' => [
        'shortcode' => 'opg',
        'label' => 'Office of the Public Guardian',
        'abbreviation' => 'OPG',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-opg@digital.justice.gov.uk',
        'wp_tag_id' => 102, //ID number from Taxonomy 'Agency' Term 'OPG'
        'links' => []
      ],
      'ospt' => [
        'shortcode' => 'ospt',
        'label' => 'Official Solicitor and Public Trustee',
        'abbreviation' => 'OSPT',
        'is_integrated' => false,
        'wp_tag_id' => 99, //ID number from Taxonomy 'Agency' Term 'HQ' as there isn;t a OSPT
        'links' => [
          [
            'url' => 'http://intranet.justice.gsi.gov.uk/ospt/index.htm',
            'label' => 'Official Solicitor and Public Trustee intranet',
            'is_external' => true
          ]
        ]
      ],
      'pb' => [
        'shortcode' => 'pb',
        'label' => 'The Parole Board',
        'abbreviation' => 'PB',
        'is_integrated' => true,
        'about_us_url' => '/about-parole-board/',
        'contact_email_address' => 'intranet-pb@digital.justice.gov.uk',
        'wp_tag_id' => 1098, //ID number from Taxonomy 'Agency' Term 'PB'
        'links' => []
      ]
    ];
    }

    /***
    * Gets the intranet code, if present
    *
    */
    public function getCurrentAgency()
    {
        $agency = isset($_COOKIE['dw_agency']) ? trim($_COOKIE['dw_agency']) : '';
        $liveAgencies = $this->getList();
        return isset($liveAgencies[$agency]) ? $liveAgencies[$agency] : $liveAgencies['hq'];
    }

    /**
     * @param string $agency
     * @return mixed
     *
     * Returns all the social links per agency. Used to live at models/follow_us.php
     */
    public static function getSocialLinks($agency = 'hq')
    {
        $links = [
            'ppo' => [
            ],
            'judicial-office' => [
              [
                  'url' => 'https://twitter.com/JudiciaryUK',
                  'label' => 'Judicial Office on Twitter',
                  'name' => 'twitter',
              ]
            ],
            'cica' => [
            ],
            'pb' => [
                [
                    'url' => 'https://twitter.com/Parole_Board',
                    'label' => 'Parole Board on Twitter',
                    'name' => 'twitter',
                ],
                [
                    'url' => 'https://www.yammer.com/paroleboard.gsi.gov.uk',
                    'label' => 'Parole Board on Yammer',
                    'name' => 'yammer',
                ]
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
            'law-commission' => [
                [
                    'url' => 'https://twitter.com/Law_Commission',
                    'label' => 'Law Commission on Twitter',
                    'name' => 'twitter',
                ],
                [
                    'url' => 'https://www.linkedin.com/company/law-commission-england-and-wales-',
                    'label' => 'Law Commission on LinkedIn',
                    'name' => 'linkedin',
                ]
            ],
            'hmcts' => [
                [
                    'url' => 'https://twitter.com/CEOofHMCTS',
                    'label' => 'HMCTS CEO on Twitter',
                    'name' => 'twitter',
                ],
                [
                    'url' => 'https://twitter.com/hmctsgovuk',
                    'label' => 'HMCTS on Twitter',
                    'name' => 'twitter',
                ],
                [
                    'url' => 'https://www.yammer.com/hmcts.gsi.gov.uk',
                    'label' => 'HMCTS on Yammer',
                    'name' => 'yammer',
                ],
                [
                    'url' => 'https://www.linkedin.com/company/11011994',
                    'label' => 'HMCTS on LinkedIn',
                    'name' => 'linkedin',
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


        return $links[$agency];
    }
}
