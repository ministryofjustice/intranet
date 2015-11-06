<?php

class My_moj_model extends MVC_model {
  function get_data() {
    return array(
      'departments' => array(
        array(
          'name' => 'hm-courts-and-tribunals-service',
          'label' => 'HM Courts &amp; Tribunals Service',
          'url' => 'http://libra.lcd.gsi.gov.uk/hmcts/index.htm'
        ),
        array(
          'name' => 'judicial-appointments-commission',
          'label' => 'Judicial Appointments Commission',
          'url' => 'http://jac.intranet.service.justice.gov.uk/'
        ),
        array(
          'name' => 'judicial-office',
          'label' => 'Judicial Office',
          'url' => 'http://judicialoffice.intranet.service.justice.gov.uk/'
        ),
        array(
          'name' => 'law-commission',
          'label' => 'Law Commission',
          'url' => 'http://lawcommission.intranet.service.justice.gov.uk/'
        ),
        array(
          'name' => 'legal-aid-agency',
          'label' => 'Legal Aid Agency',
          'url' => 'http://intranet.justice.gsi.gov.uk/laa/'
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
          'url' => 'https://peoplefinder.service.gov.uk/',
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
          'url' => site_url('/jobs/'),
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
          'title' => 'The MoJ Story',
          'icon' => 'moj-story',
          'url' => site_url('/about-us/moj-story/'),
          'external' => false
        ),
        array(
          'title' => 'Webchats',
          'icon' => 'webchat',
          'url' => site_url('/webchats/'),
          'external' => false
        )
      )
    );
  }
}
