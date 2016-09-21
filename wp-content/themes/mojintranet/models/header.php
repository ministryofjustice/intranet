<?php if (!defined('ABSPATH')) die();

class Header_model extends MVC_model {
  function get_data() {
    return array(
      'stringified_agencies' => htmlspecialchars(json_encode($this->_get_agencies())),
      'main_menu' => $this->model->menu->get_menu_items([
        'location' => 'main-menu',
        'post_id' => true
      ])
    );
  }
  function get_content_agency() {
    $content_agency = 'shared';

    if(!is_page(['blog','guidance','newspage','events','search-results','about-us']) && !is_front_page()) {
      global $post;

      $agency_terms = get_the_terms($post->ID, 'agency');

      if (count($agency_terms) > 0) {
        $agency_slugs = [];
        
        foreach ($agency_terms as $agency) {
          $agency_slugs[] = $agency->slug;
        }

        if (in_array('hq',$agency_slugs)) {
          $content_agency = 'hq';
        }
        else {
          $content_agency = $agency_slugs[0];
        }
      }
    }
    return $content_agency;
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
      'cica' => array(
        'label' => 'Criminal Injuries Compensation Authority',
        'abbreviation' => 'CICA',
        'url' => '',
        'is_integrated' => true,
        'is_external' => false
      ),
      'pb' => array(
        'label' => 'Parole Board',
        'abbreviation' => 'PB',
        'url' => '',
        'is_integrated' => true,
        'is_external' => false
      ),
      'hmcts' => array(
        'label' => 'HM Courts &amp; Tribunals Service',
        'abbreviation' => 'HMCTS',
        'url' => 'http://hmcts.intranet.service.justice.gov.uk/hmcts/',
        'url_label' => 'HMCTS Archive intranet',
        'blog_url' => 'http://hmcts.blogs.justice.gov.uk',
        'is_integrated' => true,
        'is_external' => true
      ),
      'judicial-appointments-commission' => array(
        'label' => 'Judicial Appointments Commission',
        'abbreviation' => 'JAC',
        'url' => 'http://jac.intranet.service.justice.gov.uk/',
        'is_integrated' => false,
        'is_external' => true
      ),
      'judicial-office' => array(
        'label' => 'Judicial Office',
        'abbreviation' => 'JO',
        'url' => 'http://judicialoffice.intranet.service.justice.gov.uk/',
        'is_integrated' => false,
        'is_external' => true
      ),
      'law-commission' => array(
        'label' => 'Law Commission',
        'abbreviation' => 'LawCom',
        'url' => 'http://lawcommission.intranet.service.justice.gov.uk/',
        'is_integrated' => false,
        'is_external' => true
      ),
      'laa' => array(
        'label' => 'Legal Aid Agency',
        'abbreviation' => 'LAA',
        'url' => '',
        'is_integrated' => true,
        'is_external' => true
      ),
      'hq' => array(
        'label' => 'Ministry of Justice HQ',
        'abbreviation' => 'MoJ',
        'url' => site_url('/about-us/moj-transformation/'),
        'url_label' => 'MoJ TRANSFORMATION &#8594;',
        'is_integrated' => true,
        'default' => true,
        'is_external' => false,
        'classes' => 'transformation'
      ),
      'noms' => array(
        'label' => 'National Offender Management Service',
        'abbreviation' => 'NOMS',
        'url' => 'https://intranet.noms.gsi.gov.uk/',
        'is_integrated' => false,
        'is_external' => true
      ),
      'nps' => array(
        'label' => 'National Probation Service',
        'abbreviation' => 'NPS',
        'url' => 'https://intranet.noms.gsi.gov.uk/',
        'is_integrated' => false,
        'is_external' => true
      ),
      'opg' => array(
        'label' => 'Office of the Public Guardian',
        'abbreviation' => 'OPG',
        'url' => '',
        'is_integrated' => true,
        'is_external' => true
      ),
      'ospt' => array(
        'label' => 'Official Solicitor and Public Trustee',
        'abbreviation' => 'OSPT',
        'url' => 'http://intranet.justice.gsi.gov.uk/ospt/index.htm',
        'is_integrated' => false,
        'is_external' => true
      )
    );
  }
}
