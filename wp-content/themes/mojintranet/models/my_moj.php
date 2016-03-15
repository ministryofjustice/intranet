<?php

class My_moj_model extends MVC_model {
  function get_data() {
    return array(
      'apps' => array(
        array(
          'title' => 'People finder',
          'icon' => 'people-finder',
          'url' => 'https://peoplefinder.service.gov.uk/',
          'external' => true
        ),
        array(
          'title' => 'Travel booking',
          'icon' => 'travel-booking',
          'url' => 'https://www.trips.uk.com/js/SABS/Corporate.html',
          'external' => true
        ),
        array(
          'title' => 'Jobs',
          'icon' => 'jobs',
          'url' => site_url('/jobs/'),
          'external' => true
        ),
        array(
          'title' => 'Pensions',
          'icon' => 'pension',
          'url' => 'http://www.civilservicepensionscheme.org.uk/',
          'external' => true
        ),
        array(
          'title' => 'Phoenix',
          'icon' => 'phoenix',
          'url' => site_url('/phoenix/'),
          'external' => false
        ),
        array(
          'title' => 'Civil Service Learning',
          'icon' => 'civil-service-learning',
          'url' => 'https://civilservicelearning.civilservice.gov.uk/',
          'external' => true
        ),
        array(
          'title' => 'IT portal',
          'icon' => 'it-portal',
          'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
          'external' => true
        ),
        array(
          'title' => 'MoJ Webchat',
          'icon' => 'webchat',
          'url' => site_url('/webchats/'),
          'external' => false
        )
      )
    );
  }

  public function get_quick_links() {
    $data = array();

    $menu_items = wp_get_nav_menu_items('my-moj-quick-links');

    foreach ($menu_items as $menu_item) {
      $quick_link['title'] = $menu_item->title;
      $quick_link['url'] = $menu_item->url;
      $data['results'][] = $quick_link;
    }

    return $data;
  }
}
