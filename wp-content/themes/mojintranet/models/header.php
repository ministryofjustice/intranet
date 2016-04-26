<?php if (!defined('ABSPATH')) die();

class Header_model extends MVC_model {
  function get_data() {
    return array(
      'departments' => array(
        array(
          'name' => 'hmcts',
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
          'name' => 'laa',
          'label' => 'Legal Aid Agency',
          'url' => 'http://intranet.justice.gsi.gov.uk/laa/'
        ),
        array(
          'name' => 'hq',
          'label' => 'Ministry of Justice HQ',
          'url' => '',
          'default' => true
        ),
        array(
          'name' => 'noms',
          'label' => 'National Offender Management Service',
          'url' => 'https://intranet.noms.gsi.gov.uk/'
        ),
        array(
          'name' => 'nps',
          'label' => 'National Probation Service',
          'url' => 'https://intranet.noms.gsi.gov.uk/'
        ),
        array(
          'name' => 'opg',
          'label' => 'Office of the Public Guardian',
          'url' => 'http://intranet.justice.gsi.gov.uk/opg/index.htm'
        ),
        array(
          'name' => 'ospt',
          'label' => 'Official Solicitor and Public Trustee',
          'url' => 'http://intranet.justice.gsi.gov.uk/ospt/index.htm'
        )
      ),
    );
  }
}
