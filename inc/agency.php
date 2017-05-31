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

  public function getCurrentAgency()
  {
      $agency = isset($_COOKIE['dw_agency']) ? trim ($_COOKIE['dw_agency']) : '';

      $liveAgencies = $this->getList();

      return isset($liveAgencies[$agency]) ? $liveAgencies[$agency] : $liveAgencies['hq'];
  }

}
