<?php
namespace MOJ\Intranet;

if (!defined('ABSPATH')) die();

class Agency {

  function getContactEmailAdress($agency = 'hq') {
    $agencies = $this->getList();

    if (isset($agencies[$agency]) && $agencies[$agency]['is_integrated'] == true && !empty($agencies[$agency]['contact_email_address'])) {
      return $agencies[$agency]['contact_email_address'];
    }
    else {
      return $agencies['hq']['contact_email_address'];
    }
  }
  function getList() {
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
    return array(
      'cica' => array(
        'shortcode' => 'cica',
        'label' => 'Criminal Injuries Compensation Authority',
        'abbreviation' => 'CICA',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-cica@digital.justice.gov.uk',
        'links' => []
      ),
      'hmcts' => array(
        'shortcode' => 'hmcts',
        'label' => 'HM Courts &amp; Tribunals Service',
        'abbreviation' => 'HMCTS',
        'blog_url' => 'http://hmcts.blogs.justice.gov.uk',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-hmcts@digital.justice.gov.uk',
        'links' => [
          [
            'url' => 'http://hmcts.intranet.service.justice.gov.uk/hmcts/',
            'label' => 'HMCTS Archive intranet',
            'is_external' => true
          ],
          [
            'url' => site_url('/about-hmcts/justice-matters/'),
            'label' => 'Justice Matters',
            'classes' => 'transformation'

          ]
        ]
      ),
      'judicial-appointments-commission' => array(
        'shortcode' => 'judicial-appointments-commission',
        'label' => 'Judicial Appointments Commission',
        'abbreviation' => 'JAC',
        'is_integrated' => false,
        'links' => [
          [
            'url' => 'http://jac.intranet.service.justice.gov.uk/',
            'label' => 'Judicial Appointments Commission intranet',
            'is_external' => true
          ]
        ]
      ),
      'judicial-office' => array(
        'shortcode' => 'judicial-office',
        'label' => 'Judicial Office',
        'abbreviation' => 'JO',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-jo@digital.justice.gov.uk',
        'links' => [
          [
            'url' => 'http://judicialoffice.intranet.service.justice.gov.uk/',
            'label' => 'Judicial Office intranet',
            'is_external' => true
          ]
        ]
      ),
      'law-commission' => array(
        'shortcode' => 'law-commission',
        'label' => 'Law Commission',
        'abbreviation' => 'LawCom',
        'is_integrated' => false,
        'links' => [
          [
            'url' => 'http://lawcommission.intranet.service.justice.gov.uk/',
            'label' => 'Law Commission intranet',
            'is_external' => true
          ]
        ]
      ),
      'laa' => array(
        'shortcode' => 'laa',
        'label' => 'Legal Aid Agency',
        'abbreviation' => 'LAA',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-laa@digital.justice.gov.uk',
        'links' => []
      ),
      'hq' => array(
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
      ),
      'noms' => array(
        'shortcode' => 'noms',
        'label' => 'National Offender Management Service',
        'abbreviation' => 'NOMS',
        'is_integrated' => false,
        'links' => [
          [
            'url' => 'https://intranet.noms.gsi.gov.uk/',
            'label' => 'National Offender Management Service intranet',
            'is_external' => true
          ]
        ]
      ),
      'nps' => array(
        'shortcode' => 'nps',
        'label' => 'National Probation Service',
        'abbreviation' => 'NPS',
        'is_integrated' => false,
        'links' => [
          [
            'url' => 'https://intranet.noms.gsi.gov.uk/',
            'label' => 'National Probation Service intranet',
            'is_external' => true
          ]
        ]
      ),
      'opg' => array(
        'shortcode' => 'opg',
        'label' => 'Office of the Public Guardian',
        'abbreviation' => 'OPG',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-opg@digital.justice.gov.uk',
        'links' => []
      ),
      'ospt' => array(
        'shortcode' => 'ospt',
        'label' => 'Official Solicitor and Public Trustee',
        'abbreviation' => 'OSPT',
        'is_integrated' => false,
        'links' => [
          [
            'url' => 'http://intranet.justice.gsi.gov.uk/ospt/index.htm',
            'label' => 'Official Solicitor and Public Trustee intranet',
            'is_external' => true
          ]
        ]
      ),
      'pb' => array(
        'shortcode' => 'pb',
        'label' => 'The Parole Board',
        'abbreviation' => 'PB',
        'is_integrated' => true,
        'about_us_url' => '/about-parole-board/',
        'contact_email_address' => 'intranet-pb@digital.justice.gov.uk',
        'links' => []
      ),
      'ppo' => array(
        'shortcode' => 'ppo',
        'label' => 'Prisons and Probations Ombudsman',
        'abbreviation' => 'PPO',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-ppo@digital.justice.gov.uk',
        'links' => []
      )
    );
  }

  /***
   * Gets the intranet code, if present
   *
   */

  function getCurrentAgency()
  {
      $agency = isset($_COOKIE['dw_agency']) ? trim ($_COOKIE['dw_agency']) : '';

      $liveAgencies = $this->getList();

      return isset($liveAgencies[$agency]) ? $liveAgencies[$agency] : $liveAgencies['hq'];
  }

  /**
   * Gets the apps enabled for each agency
   *
   *
   */

  public static function getApps($agency = 'hq') {
        $apps = [
            'ppo' => [
            ],
            'judicial-office' => [
            ],
            'cica' => [
                [
                    'title' => 'People finder',
                    'icon' => 'people-finder',
                    'url' => 'https://peoplefinder.service.gov.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'Civil Service Learning',
                    'icon' => 'civil-service-learning',
                    'url' => 'https://civilservicelearning.civilservice.gov.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'SOP',
                    'icon' => 'sop',
                    'url' => site_url('/sop/'),
                    'external' => false,
                ],
                [
                    'title' => 'Jobs',
                    'icon' => 'jobs',
                    'url' => site_url('/jobs/'),
                    'external' => false,
                ],
                [
                    'title' => 'Mitrefinch',
                    'icon' => 'mitrefinch',
                    'url' => 'http://cica-mitrefinch/tms/silverlight.aspx',
                    'external' => true,
                ],
                [
                    'title' => 'IT Self Service Portal',
                    'icon' => 'it-portal',
                    'url' => 'http://cicald-web/ServiceDesk.WebAccess/ss/Dashboard/OpenHomeDashboard.rails?id=c2492b87-2b72-4068-8983-0e1e2a2e6a15',
                    'external' => true,
                ],
                [
                    'title' => 'Bright Ideas',
                    'icon' => 'bright-ideas',
                    'url' => 'http://cica-mitrefinch/tms/silverlight.aspx',
                    'external' => true,
                ],
            ],
            'pb' => [
                [
                    'title' => 'People finder',
                    'icon' => 'people-finder',
                    'url' => 'https://peoplefinder.service.gov.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'SOP',
                    'icon' => 'sop',
                    'url' => site_url('/sop/'),
                    'external' => false,
                ],
                [
                    'title' => 'Civil Service Learning',
                    'icon' => 'civil-service-learning',
                    'url' => 'https://civilservicelearning.civilservice.gov.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'Jobs',
                    'icon' => 'jobs',
                    'url' => site_url('/jobs/'),
                    'external' => false,
                ],
                [
                    'title' => 'Parole Board public site',
                    'icon' => 'govuk',
                    'url' => 'https://www.gov.uk/government/organisations/parole-board',
                    'external' => true,
                ],
            ],
            'hq' => [
                [
                    'title' => 'People finder',
                    'icon' => 'people-finder',
                    'url' => 'https://peoplefinder.service.gov.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'Travel booking',
                    'icon' => 'travel-booking',
                    'url' => 'https://www.trips.uk.com/js/SABS/Corporate.html',
                    'external' => true,
                ],
                [
                    'title' => 'Jobs',
                    'icon' => 'jobs',
                    'url' => site_url('/jobs/'),
                    'external' => false,
                ],
                [
                    'title' => 'Pensions',
                    'icon' => 'pension',
                    'url' => 'http://www.civilservicepensionscheme.org.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'SOP',
                    'icon' => 'sop',
                    'url' => site_url('/sop/'),
                    'external' => false,
                ],
                [
                    'title' => 'Civil Service Learning',
                    'icon' => 'civil-service-learning',
                    'url' => 'https://civilservicelearning.civilservice.gov.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'IT portal',
                    'icon' => 'it-portal',
                    'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
                    'external' => true,
                ],
                [
                    'title' => 'MoJ Webchat',
                    'icon' => 'webchat',
                    'url' => site_url('/webchats/'),
                    'external' => false,
                ],
                [
                    'title' => 'Room Booking',
                    'icon' => 'room-booking',
                    'url' => 'https://app.matrixbooking.com/',
                    'external' => true,
                ],
            ],
            'hmcts' => [
                [
                    'title' => 'SOP',
                    'icon' => 'sop',
                    'url' => site_url('/sop/'),
                    'external' => false,
                ],
                [
                    'title' => 'Travel bookings (Redfern)',
                    'icon' => 'travel-booking',
                    'url' => site_url('/guidance/commercial-contract-management/how-to-obtain-goods-and-services/travel/'),
                    'external' => false,
                ],
                [
                    'title' => 'Jobs',
                    'icon' => 'jobs',
                    'url' => site_url('/jobs/'),
                    'external' => false,
                ],
                [
                    'title' => 'IT portal',
                    'icon' => 'it-portal',
                    'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
                    'external' => true,
                ],
                [
                    'title' => 'Court and Tribunal Finder',
                    'icon' => 'court-finder',
                    'url' => 'https://courttribunalfinder.service.gov.uk/search/',
                    'external' => true,
                ],
                [
                    'title' => 'Form Finder',
                    'icon' => 'form-finder',
                    'url' => 'http://hmctsformfinder.justice.gov.uk/HMCTS/FormFinder.do',
                    'external' => true,
                ],
            ],
            'laa' => [
                [
                    'title' => 'People finder',
                    'icon' => 'people-finder',
                    'url' => 'https://peoplefinder.service.gov.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'Jobs',
                    'icon' => 'jobs',
                    'url' => site_url('/guidance/human-resources/internal-job-vacancies-2/'),
                    'external' => false,
                ],
                [
                    'title' => 'IT portal',
                    'icon' => 'it-portal',
                    'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
                    'external' => true,
                ],
                [
                    'title' => 'Civil Service Learning',
                    'icon' => 'civil-service-learning',
                    'url' => 'https://civilservicelearning.civilservice.gov.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'LAA online portal',
                    'icon' => 'laa-online-portal',
                    'url' => 'https://lsconline.lab.gov',
                    'external' => true,
                ],
                [
                    'title' => 'SOP',
                    'icon' => 'sop',
                    'url' => site_url('/sop/'),
                    'external' => false,
                ],
                [
                    'title' => 'LAA manual (civil)',
                    'icon' => 'laa-manual-civil',
                    'url' => 'https://www.gov.uk/guidance/civil-legal-aid-civil-regulations-civil-contracts-and-guidance',
                    'external' => true,
                ],
                [
                    'title' => 'LAA manual (crime)',
                    'icon' => 'laa-manual-crime',
                    'url' => 'https://www.gov.uk/guidance/criminal-legal-aid-crime-regulations-crime-contracts-and-guidance',
                    'external' => true,
                ],
                [
                    'title' => 'Means Assessment Administration Tool',
                    'icon' => 'maat',
                    'url' => 'https://meansassessment.legalservices.gov.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'Public LAA site',
                    'icon' => 'govuk',
                    'url' => 'https://www.gov.uk/government/organisations/legal-aid-agency',
                    'external' => true,
                ],
                [
                    'title' => 'Provider Information',
                    'icon' => 'mi-hub',
                    'url' => site_url('/guidance/management-information-2/provider-information/'),
                    'external' => false,
                ],
            ],
            'opg' => [
                [
                    'title' => 'Etarmis',
                    'icon' => 'etarmis',
                    'url' => 'https://opg-flexi.org.uk/FCDWeb/',
                    'external' => true,
                ],
                [
                    'title' => 'Jobs',
                    'icon' => 'jobs',
                    'url' => 'https://www.civilservicejobs.service.gov.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'IT portal',
                    'icon' => 'it-portal',
                    'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
                    'external' => true,
                ],
                [
                    'title' => 'Civil Service Learning',
                    'icon' => 'civil-service-learning',
                    'url' => 'https://civilservicelearning.civilservice.gov.uk/',
                    'external' => true,
                ],
                [
                    'title' => 'SOP',
                    'icon' => 'sop',
                    'url' => site_url('/sop/'),
                    'external' => false,
                ],
                [
                    'title' => 'OPG GOV.UK site',
                    'icon' => 'govuk',
                    'url' => 'https://www.gov.uk/government/organisations/office-of-the-public-guardian',
                    'external' => true,
                ],
                [
                    'title' => 'People finder',
                    'icon' => 'people-finder',
                    'url' => 'https://peoplefinder.service.gov.uk/',
                    'external' => true,
                ],
            ]
        ];

        $app = isset($apps[$agency]) ? $apps[$agency] : $apps['hq'];
        return $app;
    }
}
