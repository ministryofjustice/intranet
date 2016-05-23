<?php if (!defined('ABSPATH')) die();

class Header_model extends MVC_model {
  function get_data() {
    return array(
      'stringified_agencies' => htmlspecialchars(json_encode($this->_get_agencies())),
    );
  }

  private function _get_agencies() {
    return array(
      'hmcts' => array(
        'label' => 'HM Courts &amp; Tribunals Service',
        'url' => 'http://libra.lcd.gsi.gov.uk/hmcts/index.htm',
        'is_integrated' => true
      ),
      'judicial-appointments-commission' => array(
        'label' => 'Judicial Appointments Commission',
        'url' => 'http://jac.intranet.service.justice.gov.uk/',
        'is_integrated' => false
      ),
      'judicial-office' => array(
        'label' => 'Judicial Office',
        'url' => 'http://judicialoffice.intranet.service.justice.gov.uk/',
        'is_integrated' => false
      ),
      'law-commission' => array(
        'label' => 'Law Commission',
        'url' => 'http://lawcommission.intranet.service.justice.gov.uk/',
        'is_integrated' => false
      ),
      'laa' => array(
        'label' => 'Legal Aid Agency',
        'url' => 'http://intranet.justice.gsi.gov.uk/laa/',
        'is_integrated' => false
      ),
      'hq' => array(
        'label' => 'Ministry of Justice HQ',
        'url' => '',
        'is_integrated' => true,
        'default' => true
      ),
      'noms' => array(
        'label' => 'National Offender Management Service',
        'url' => 'https://intranet.noms.gsi.gov.uk/',
        'is_integrated' => false
      ),
      'nps' => array(
        'label' => 'National Probation Service',
        'url' => 'https://intranet.noms.gsi.gov.uk/',
        'is_integrated' => false
      ),
      'opg' => array(
        'label' => 'Office of the Public Guardian',
        'url' => 'http://intranet.justice.gsi.gov.uk/opg/index.htm',
        'is_integrated' => false
      ),
      'ospt' => array(
        'label' => 'Official Solicitor and Public Trustee',
        'url' => 'http://intranet.justice.gsi.gov.uk/ospt/index.htm',
        'is_integrated' => false
      )
    );
  }
}
