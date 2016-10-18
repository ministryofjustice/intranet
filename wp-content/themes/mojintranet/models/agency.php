<?php if (!defined('ABSPATH')) die();

class Agency_model extends MVC_model {
  function get_contact_email_address($agency = 'hq') {
    $agencies = $this->get_list();

    if (isset($agencies[$agency]) && $agencies[$agency]['is_integrated'] == true && !empty($agencies[$agency]['contact_email_address'])) {
      return $agencies[$agency]['contact_email_address'];
    }
    else {
      return $agencies['hq']['contact_email_address'];
    }
  }
  function get_list() {
    /**
     * Agency array structure:
     *
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
        'label' => 'Criminal Injuries Compensation Authority',
        'abbreviation' => 'CICA',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-cica@digital.justice.gov.uk',
        'links' => []
      ),
      'pb' => array(
        'label' => 'Parole Board',
        'abbreviation' => 'PB',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-pb@digital.justice.gov.uk',
        'links' => []
      ),
      'hmcts' => array(
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
        'label' => 'Legal Aid Agency',
        'abbreviation' => 'LAA',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-laa@digital.justice.gov.uk',
        'links' => []
      ),
      'hq' => array(
        'label' => 'Ministry of Justice HQ',
        'abbreviation' => 'MoJ',
        'is_integrated' => true,
        'default' => true,
        'contact_email_address' => 'intranet@justice.gsi.gov.uk',
        'links' => [
          [
            'url' => site_url('/about-us/moj-transformation/'),
            'label' => 'MoJ TRANSFORMATION &#8594;',
            'is_external' => false,
            'classes' => 'transformation'
          ]
        ]
      ),
      'noms' => array(
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
        'label' => 'Office of the Public Guardian',
        'abbreviation' => 'OPG',
        'is_integrated' => true,
        'contact_email_address' => 'intranet-opg@digital.justice.gov.uk',
        'links' => []
      ),
      'ospt' => array(
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
      )
    );
  }
}
