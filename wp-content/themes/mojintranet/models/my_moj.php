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

    $menu_items = get_field($agency . '_quick_links', 'option');

    if (isset($menu_items)) {
      foreach ($menu_items as $menu_item) {
        $quick_link['title'] = $menu_item['quick_link_title'];
        $quick_link['url'] = $menu_item['quick_link_url'];
        $data[] = $quick_link;
      }
    }

    return $data;
  }

  private function get_apps($agency = 'hq') {
    $apps = [
      'ppo' => [
      ],
      'judicial-office' => [
      ],
      'cica' => [
      ],
      'pb' => [
        [
          'title' => 'People finder',
          'icon' => 'people-finder',
          'url' => 'https://peoplefinder.service.gov.uk/',
          'external' => true,
        ],
        [
          'title' => 'Phoenix',
          'icon' => 'phoenix',
          'url' => 'http://phoenix.ps.gov.uk:8000/OA_HTML/AppsLocalLogin.jsp?requestUrl=APPSHOMEPAGE&cancelUrl=http%3A%2F%2Fphoenix.ps.gov.uk%3A8000%2Foa_servlets%2Foracle.apps.fnd.sso.AppsLogin&s2=D9E6D249A1E2875B06EA4A1B544B7ECDF9FA139755032BAAD5A6B9E1096BD6DF',
          'external' => true,
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
          'url' => 'https://www.gov.uk/government/organisations/legal-aid-agency',
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
          'title' => 'Phoenix',
          'icon' => 'phoenix',
          'url' => 'http://phoenix.ps.gov.uk:8000/OA_HTML/AppsLocalLogin.jsp?requestUrl=APPSHOMEPAGE',
          'external' => true,
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

    return $apps[$agency];
  }
}
