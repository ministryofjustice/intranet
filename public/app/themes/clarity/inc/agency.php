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
        *      - main (boolean) - Should this link be used for the agency switcher?
        *  - has_archive (boolean) (optional) - is the intranet archive available for this agency?
        */

        $agencies_array = [
            'cica' => [
                'shortcode' => 'cica',
                'label' => 'Criminal Injuries Compensation Authority',
                'abbreviation' => 'CICA',
                'is_integrated' => true,
                'contact_email_address' => 'intranet-cica@digital.justice.gov.uk',
                'links' => [],
            ],
            'hmcts' => [
                'shortcode' => 'hmcts',
                'label' => 'HM Courts &amp; Tribunals Service',
                'abbreviation' => 'HMCTS',
                'blog_url' => '/blog/',
                'is_integrated' => true,
                'contact_email_address' => 'intranet-hmcts@digital.justice.gov.uk',
                'links' => [
                    [
                        'url' => site_url('/about-hmcts/justice-matters/'),
                        'label' => 'Justice Matters',
                        'classes' => 'transformation',
                        'is_external' => true
                    ]
                ],
                'has_archive' => true
            ],
            'noms' => [
                'shortcode' => 'noms',
                'label' => 'HM Prison & Probation Service',
                'abbreviation' => 'HMPPS',
                'is_integrated' => false,
                'links' => [
                    [
                        'url' => 'https://justiceuk.sharepoint.com/sites/HMPPSIntranet',
                        'label' => 'HM Prison & Probation Service intranet',
                        'main' => true,
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
                'links' => []
            ],
            'law-commission' => [
                'shortcode' => 'law-commission',
                'label' => 'Law Commission',
                'abbreviation' => 'LawCom',
                'is_integrated' => true,
                'links' => []
            ],
            'laa' => [
                'shortcode' => 'laa',
                'label' => 'Legal Aid Agency',
                'abbreviation' => 'LAA',
                'is_integrated' => true,
                'contact_email_address' => 'intranet-laa@digital.justice.gov.uk',
                'links' => []
            ],
            'hq' => [
                'shortcode' => 'hq',
                'label' => 'Ministry of Justice HQ',
                'abbreviation' => 'MoJ',
                'is_integrated' => true,
                'default' => true,
                'contact_email_address' => 'intranet@justice.gsi.gov.uk',
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
                'links' => []
            ],
            'pb' => [
                'shortcode' => 'pb',
                'label' => 'The Parole Board',
                'abbreviation' => 'PB',
                'is_integrated' => true,
                'about_us_url' => '/about-parole-board/',
                'contact_email_address' => 'intranet-pb@digital.justice.gov.uk',
                'links' => []
            ],
            'ospt' => [
                'shortcode' => 'ospt',
                'label' => 'Official Solicitor and Public Trustee',
                'abbreviation' => 'OSPT',
                'is_integrated' => true,
                'contact_email_address' => '',
                'links' => []
            ],
            'jac' => [
                'shortcode' => 'jac',
                'label' => 'Judicial Appointments Commission',
                'abbreviation' => 'JAC',
                'is_integrated' => true,
                'contact_email_address' => 'communications@judicialappointments.gov.uk',
                'links' => []
            ],
            'ima' => [
                'shortcode' => 'ima',
                'label' => 'Independent Monitoring Authority',
                'abbreviation' => 'IMA',
                'is_integrated' => false,
                'contact_email_address' => '',
                'links' => [
                    [
                        'url' => 'https://myima.ima-citizensrights.org.uk',
                        'label' => 'Independent Monitoring Authority intranet',
                        'main' => true,
                        'is_external' => true
                    ]
                ]
            ],
            'yjbrh' => [
                'shortcode' => 'yjbrh',
                'label' => 'Youth Justice Board Resource Hub',
                'abbreviation' => 'YJBRH',
                'is_integrated' => false,
                'contact_email_address' => '',
                'links' => [
                    [
                        'url' => 'https://yjresourcehub.uk/',
                        'label' => 'Youth Justice Board Resource Hub intranet',
                        'main' => true,
                        'is_external' => true
                    ]
                ]
            ],
            'ycs' => [
                'shortcode' => 'ycs',
                'label' => 'Youth Custody Service',
                'abbreviation' => 'YCS',
                'is_integrated' => false,
                'contact_email_address' => '',
                'links' => [
                    [
                        'url' => 'https://justiceuk.sharepoint.com/sites/HMPPSIntranet-YCS',
                        'label' => 'Youth Custody Service',
                        'main' => true,
                        'is_external' => true
                    ]
                ]
            ]
        ];

        // Dynamically populate tag_ids for each agency
        foreach ($agencies_array as $agency) {
            $shortcode = $agency['shortcode'] ?? 'hq';
            $tag = get_term_by('slug', $shortcode, 'agency');
            $tag_id = $tag->term_id ?? '';
            $agencies_array[$shortcode]['wp_tag_id'] = $tag_id;
        }

        return $agencies_array;
    }

    /*
     * Check if the agency cookie is set
     */
    public function hasAgencyCookie(): bool
    {
        return !empty($_COOKIE['dw_agency']);
    }

    /***
     * Get the agency from cookie, and make sure it's in
     * the list, otherwise default to HQ
     *
     */
    public function getCurrentAgency()
    {
        $agency = $_COOKIE['dw_agency'] ?? '';
        $liveAgencies = $this->getList();
        return $liveAgencies[trim($agency)] ?? $liveAgencies['hq'];
    }

    /**
     * Check the agency exists
     */
    public function agencyExists($agency): bool
    {
        // Check we have a valid agency
        return array_key_exists($agency, $this->getList());
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
                    'url' => 'https://www.youtube.com/user/MinistryofJusticeUK',
                    'label' => 'MoJ on YouTube',
                    'name' => 'youtube',
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
                ],
                [
                    'url' => 'https://www.youtube.com/channel/UC5Altka7XMeXog5ZFzBT9dA',
                    'label' => 'HMCTS on YouTube',
                    'name' => 'youtube',
                ],
                [
                    'url' => 'https://twitter.com/HMCTSCareers',
                    'label' => 'HMCTS Careers Twitter',
                    'name' => 'twitter',
                ],
                [
                    'url' => 'https://twitter.com/GLlTEMgovuk',
                    'label' => 'Welsh Language Twitter',
                    'name' => 'twitter',
                ],
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
                ],
                'ospt' => [
                    [
                        'url' => '',
                        'label' => '',
                        'name' => '',
                    ]
                ]
            ]
        ];


        return $links[$agency];
    }
}
