<?php
/**
 * The template for displaying Search Results pages.
 *
 * Template name: Home page
 */
class Page_home extends MVC_controller {
  function main() {
    if(have_posts()) the_post();
    $this->view('layouts/default', $this->get_data());
  }

  private function get_data() {
    return array(
      'page' => 'pages/homepage/main',
      'page_data' => array(
        'emergency_message' => $this->get_emergency_message(),
        'my_moj' => $this->get_my_moj()
      )
    );
  }

  private function get_emergency_message() {
    $visible = get_option("emergency_toggle");
    $title = get_option("emergency_title");
    $date = get_option("emergency_date");
    $message = get_option("homepage_control_emergency_message");
    $message = apply_filters('the_content', $message, true);
    $type = get_option("emergency_type");

    return array(
      'visible'     => $visible,
      'title'       => $title,
      'date'        => $date,
      'message'     => $message,
      'type'        => $type
    );
  }

  private function get_my_moj() {
    return array(
      'departments' => array(
        array(
          'name' => 'legal-aid-agency',
          'label' => 'Legal Aid Agency',
          'url' => 'http://intranet.justice.gsi.gov.uk/laa/index.htm'
        ),
        array(
          'name' => 'hm-courts-and-tribunals-service',
          'label' => 'HM Courts &amp; Tribunals Service',
          'url' => 'http://libra.lcd.gsi.gov.uk/hmcts/index.htm'
        ),
        array(
          'name' => 'judicial-appointments-commission',
          'label' => 'Judicial Appointments Commission',
          'url' => 'http://intranet.justice.gsi.gov.uk/jac/index.htm'
        ),
        array(
          'name' => 'judicial-office',
          'label' => 'Judicial Office',
          'url' => 'http://intranet.justice.gsi.gov.uk/joew/index.htm'
        ),
        array(
          'name' => 'law-commission',
          'label' => 'Law Commission',
          'url' => 'http://intranet.justice.gsi.gov.uk/lawcommission/index.htm'
        ),
        array(
          'name' => 'opg',
          'label' => 'OPG',
          'url' => 'http://intranet.justice.gsi.gov.uk/opg/index.htm'
        ),
        array(
          'name' => 'ospt',
          'label' => 'OSPT',
          'url' => 'http://intranet.justice.gsi.gov.uk/ospt/index.htm'
        ),
        array(
          'name' => 'probation',
          'label' => 'Probation',
          'url' => 'http://npsintranet.probation.gsi.gov.uk/index.htm'
        )
      ),
      'apps' => array(
        array(
          'title' => 'People finder',
          'icon' => 'people-finder',
          'url' => 'http://intranet.justice.gsi.gov.uk/global/peoplefinder/',
          'external' => true
        ),
        array(
          'title' => 'Courtfinder',
          'icon' => 'courtfinder',
          'url' => 'https://courttribunalfinder.service.gov.uk/search/',
          'external' => true
        ),
        array(
          'title' => 'Jobs',
          'icon' => 'jobs',
          'url' => 'http://justice-intranet.dsd.io/jobs/',
          'external' => true
        ),
        array(
          'title' => 'IT portal',
          'icon' => 'it-portal',
          'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
          'external' => true
        ),
        array(
          'title' => 'Civil Service Learning',
          'icon' => 'civil-service-learning',
          'url' => 'https://civilservicelearning.civilservice.gov.uk/',
          'external' => true
        ),
        array(
          'title' => 'Travel booking',
          'icon' => 'travel-booking',
          'url' => 'https://www.trips.uk.com/js/SABS/Corporate.html',
          'external' => true
        ),
        array(
          'title' => 'Phoenix',
          'icon' => 'phoenix',
          'url' => site_url('/phoenix/'),
          'external' => false
        ),
        array(
          'title' => 'Pensions',
          'icon' => 'pension',
          'url' => 'http://www.civilservicepensionscheme.org.uk/',
          'external' => true
        ),
        array(
          'title' => 'Ministry of Justice',
          'icon' => 'ministry-of-justice',
          'url' => 'http://www.justice.gov.uk/',
          'external' => true
        ),
        array(
          'title' => 'GOV.UK',
          'icon' => 'gov-uk',
          'url' => 'https://www.gov.uk/',
          'external' => true
        ),
      ),
      'quick_links' => array(
        array(
          'title' => 'Annual leave',
          'url' => get_permalink(get_page_by_path('guidance-and-support/hr/leave/annual-leave')),
          'external' => false
        ),
        array(
          'title' => 'HR',
          'url' => '#',
          'external' => false
        ),
        array(
          'title' => 'Organisation',
          'url' => '#',
          'external' => false
        ),
        array(
          'title' => 'Learning &amp; Development',
          'url' => '#',
          'external' => false
        ),
        array(
          'title' => 'Statistics',
          'url' => '#',
          'external' => false
        ),
        array(
          'title' => 'Finances',
          'url' => '#',
          'external' => false
        ),
        array(
          'title' => 'Justice academy',
          'url' => '#',
          'external' => false
        )
      )
    );
  }
}

new Page_home();
