<?php if (!defined('ABSPATH')) die();

class Header_model extends MVC_model {
  function get_data() {
    return array(
      'stringified_agencies' => htmlspecialchars(json_encode($this->_get_agencies())),
    );
  }

  private function _get_agencies() {
    /** Key names and their meaning:
     * label - the full name of the agency
     * abbreviation - short name, such as HMCTS
     * url - url of the external site that goes into the My MoJ section
     * url_label - alternative label on the external link; label is used as fallback
     * blog url - custom url for main menu blog
     * is_integrated - whether the agency is already integrated into the intranet or not
     */
    return array(
      'hmcts' => array(
        'label' => 'HM Courts &amp; Tribunals Service',
        'abbreviation' => 'HMCTS',
        'url' => 'http://libra.lcd.gsi.gov.uk/hmcts/index.htm',
        'url_label' => 'HMCTS Archive',
        'blog_url' => 'http://hmcts.intranet.service.justice.gov.uk/hmcts/',
        'is_integrated' => true
      ),
      'judicial-appointments-commission' => array(
        'label' => 'Judicial Appointments Commission',
        'abbreviation' => 'JAC',
        'url' => 'http://jac.intranet.service.justice.gov.uk/',
        'is_integrated' => false
      ),
      'judicial-office' => array(
        'label' => 'Judicial Office',
        'abbreviation' => 'JO',
        'url' => 'http://judicialoffice.intranet.service.justice.gov.uk/',
        'is_integrated' => false
      ),
      'law-commission' => array(
        'label' => 'Law Commission',
        'abbreviation' => 'LawCom',
        'url' => 'http://lawcommission.intranet.service.justice.gov.uk/',
        'is_integrated' => false
      ),
      'laa' => array(
        'label' => 'Legal Aid Agency',
        'abbreviation' => 'LAA',
        'url' => 'http://intranet.justice.gsi.gov.uk/laa/',
        'is_integrated' => false
      ),
      'hq' => array(
        'label' => 'Ministry of Justice HQ',
        'abbreviation' => 'MoJ',
        'url' => '',
        'is_integrated' => true,
        'default' => true
      ),
      'noms' => array(
        'label' => 'National Offender Management Service',
        'abbreviation' => 'NOMS',
        'url' => 'https://intranet.noms.gsi.gov.uk/',
        'is_integrated' => false
      ),
      'nps' => array(
        'label' => 'National Probation Service',
        'abbreviation' => 'NPS',
        'url' => 'https://intranet.noms.gsi.gov.uk/',
        'is_integrated' => false
      ),
      'opg' => array(
        'label' => 'Office of the Public Guardian',
        'abbreviation' => 'OPG',
        'url' => 'http://intranet.justice.gsi.gov.uk/opg/index.htm',
        'is_integrated' => false
      ),
      'ospt' => array(
        'label' => 'Official Solicitor and Public Trustee',
        'abbreviation' => 'OSPT',
        'url' => 'http://intranet.justice.gsi.gov.uk/ospt/index.htm',
        'is_integrated' => false
      )
    );
  }
}
