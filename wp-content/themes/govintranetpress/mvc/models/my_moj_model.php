<?php

class My_moj_model extends MVC_model {
  function __construct() {
    echo 'My moj model initialized';
  }

  function get_data() {
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
          'url' => 'http://physmt.unisys.co.uk:8001/OA_HTML/AppsLocalLogin.jsp',
          'external' => true
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
        )
      )
    );
  }
}
