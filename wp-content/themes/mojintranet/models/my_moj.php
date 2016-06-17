<?php if (!defined('ABSPATH')) die();

class My_moj_model extends MVC_model {
  function get_data($options) {
    $agency = $options['agency'] ?: 'hq';

    return [
      'quick_links' => $this->get_quick_links($agency),
      'apps' => $this->get_apps($agency)
    ];
  }

  private function get_quick_links($agency = 'hq') {
    $data = [];

    $locations = get_nav_menu_locations();
    $menu_items = wp_get_nav_menu_items($locations[$agency . '-quick-links']);

    foreach ($menu_items as $menu_item) {
      $quick_link['title'] = $menu_item->title;
      $quick_link['url'] = $menu_item->url;
      $data[] = $quick_link;
    }

    return $data;
  }

  private function get_apps($agency = 'hq') {
    $apps = [
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
          'title' => 'Phoenix',
          'icon' => 'phoenix',
          'url' => site_url('/phoenix/'),
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
          'title' => 'Phoenix',
          'icon' => 'phoenix',
          'url' => site_url('/phoenix/'),
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
          'title' => 'MI hub',
          'icon' => 'mi-hub',
          'url' => 'http://intranet.justice.gsi.gov.uk/laa/management-information/mi-hub.htm',
          'external' => true,
        ],
        [
          'title' => 'Jobs',
          'icon' => 'jobs',
          'url' => 'http://intranet.justice.gsi.gov.uk/laa/services-and-guidance/expressions-of-interest.htm',
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
          'title' => 'LAA online portal',
          'icon' => 'laa-online-portal',
          'url' => 'https://lsconlinesso.legalservices.gov.uk/sso/pages/login.jsp',
          'external' => true,
        ],
        [
          'title' => 'Phoenix',
          'icon' => 'phoenix',
          'url' => 'http://phoenix.ps.gov.uk:8000/OA_HTML/AppsLocalLogin.jsp?requestUrl=APPSHOMEPAGE',
          'external' => true,
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
          'title' => 'M.A.A.T.',
          'icon' => 'maat',
          'url' => '',
          'external' => true,
        ],
        [
          'title' => 'MoJ Webchat',
          'icon' => 'webchat',
          'url' => site_url('/webchats/'),
          'external' => false,
        ],
      ],
      'opg' => [
        [
          'title' => 'Etarmis',
          'icon' => 'etarmis',
          'url' => 'https://opg-flexi.org.uk/FCDLogin.html',
          'external' => true,
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
          'title' => 'Civil Service Learning',
          'icon' => 'civil-service-learning',
          'url' => 'https://civilservicelearning.civilservice.gov.uk/',
          'external' => true,
        ],
        [
          'title' => 'Phoenix',
          'icon' => 'phoenix',
          'url' => 'http://phoenix.ps.gov.uk:8000/OA_HTML/AppsLocalLogin.jsp?requestUrl=APPSHOMEPAGE',
          'external' => true,
        ],
        [
          'title' => 'OPG GOV.UK site',
          'icon' => 'opg-govuk',
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

    return $apps[$agency];
  }
}
