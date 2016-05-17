<?php if (!defined('ABSPATH')) die();

class My_moj_model extends MVC_model {
  function get_data($options) {
    $agency = $options['agency'] ?: 'hq';

    return array(
      'quick_links' => $this->get_quick_links($agency),
      'apps' => $this->get_apps($agency)
    );
  }

  private function get_quick_links($agency = 'hq') {
    $data = array();

    $locations = get_nav_menu_locations();
    $menu_items = wp_get_nav_menu_items($locations[$agency . '-quick-links']);

    foreach ($menu_items as $menu_item) {
      $quick_link['title'] = $menu_item->title;
      $quick_link['url'] = $menu_item->url;
      $data[] = $quick_link;
    }

    return $data;
  }

  private function get_apps($agency = 'hq') {
    $apps = array(
      array(
        'title' => 'People finder',
        'icon' => 'people-finder',
        'url' => 'https://peoplefinder.service.gov.uk/',
        'external' => true,
        'agency' => array('hq', 'hmcts', 'opg', 'laa')
      ),
      array(
        'title' => 'Travel booking',
        'icon' => 'travel-booking',
        'url' => 'https://www.trips.uk.com/js/SABS/Corporate.html',
        'external' => true,
        'agency' => array('hq', 'hmcts', 'opg', 'laa')
      ),
      array(
        'title' => 'Jobs',
        'icon' => 'jobs',
        'url' => site_url('/jobs/'),
        'external' => true,
        'agency' => array('hq', 'hmcts', 'opg', 'laa')
      ),
      array(
        'title' => 'Pensions',
        'icon' => 'pension',
        'url' => 'http://www.civilservicepensionscheme.org.uk/',
        'external' => true,
        'agency' => array('hq', 'hmcts', 'opg', 'laa')
      ),
      array(
        'title' => 'Phoenix',
        'icon' => 'phoenix',
        'url' => site_url('/phoenix/'),
        'external' => false,
        'agency' => array('hq', 'hmcts', 'opg', 'laa')
      ),
      array(
        'title' => 'Civil Service Learning',
        'icon' => 'civil-service-learning',
        'url' => 'https://civilservicelearning.civilservice.gov.uk/',
        'external' => true,
        'agency' => array('hq', 'hmcts', 'opg', 'laa')
      ),
      array(
        'title' => 'IT portal',
        'icon' => 'it-portal',
        'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
        'external' => true,
        'agency' => array('hq', 'hmcts', 'opg', 'laa')
      ),
      array(
        'title' => 'MoJ Webchat',
        'icon' => 'webchat',
        'url' => site_url('/webchats/'),
        'external' => false,
        'agency' => array('hq', 'hmcts', 'opg', 'laa')
      ),
      array(
        'title' => 'Room Booking',
        'icon' => 'room-booking',
        'url' => 'https://app.matrixbooking.com/',
        'external' => true,
        'agency' => array('hq', 'hmcts', 'opg', 'laa')
      )
    );

    $filtered_apps = array();

    foreach($apps as $app) {
      if(in_array($agency, $app['agency'])) {
        $filtered_apps[] = $app;
      }
    }

    return $filtered_apps;
  }
}
