<?php
/**
 * The template for displaying Search Results pages.
 *
 * Template name: Home page
 */
class Page_home extends MVC_controller {
  function main() {
    get_header();
    if(have_posts()) the_post();
    $this->view('pages/homepage/main', $this->get_data());
    get_footer();
  }

  private function get_data() {
    return array(
      'emergency_message' => $this->get_emergency_message(),
      'my_moj' => $this->get_my_moj()
    );
  }

  private function get_emergency_message() {
    $visible = get_option("emergency_toggle");
    $title = get_option("emergency_title");
    $date = get_option("emergency_date");
    $message = get_option("homepage_control_emergency_message");
    $message = apply_filters('the_content', $message, true);

    return array(
      'visible'     => $visible,
      'title'       => $title,
      'date'        => $date,
      'message'     => $message,
      'type'        => strtolower(get_option("homepage_control_emergency_message_style"))
    );
  }

  private function get_my_moj() {
    return array(
      'apps' => array(
        array(
          'title' => 'People finder',
          'icon' => 'people-finder',
          'url' => 'https://people-finder.dsd.io/',
          'external' => true
        ),
        array(
          'title' => 'Courtfinder',
          'icon' => 'courtfinder',
          'url' => 'https://courttribunalfinder.service.gov.uk/',
          'external' => true
        ),
        array(
          'title' => 'Jobs',
          'icon' => 'jobs',
          'url' => '#',
          'external' => true
        ),
        array(
          'title' => 'iExpense',
          'icon' => 'iexpense',
          'url' => '#',
          'external' => true
        ),
        array(
          'title' => 'Civil Service Learning',
          'icon' => 'civil-service-learning',
          'url' => '#',
          'external' => true
        ),
        array(
          'title' => 'Travel booking',
          'icon' => 'travel-booking',
          'url' => '#',
          'external' => true
        ),
        array(
          'title' => 'Phoenix',
          'icon' => 'phoenix',
          'url' => '#',
          'external' => true
        ),
        array(
          'title' => 'Online Toolkit',
          'icon' => 'online-toolkit',
          'url' => '#',
          'external' => true
        ),
        array(
          'title' => 'HMCTS Intranet',
          'icon' => 'hmcts-intranet',
          'url' => '#',
          'external' => true
        ),
      ),
      'quick_links' => array(
        array(
          'title' => 'Annual leave',
          'url' => get_permalink(get_page_by_path('guidance-and-support/hr/leave/annual-leave')),
          'external' => false
        ),
        array(
          'title' => 'HR',
          'url' => '#',
          'external' => false
        ),
        array(
          'title' => 'Organisation',
          'url' => '#',
          'external' => false
        ),
        array(
          'title' => 'Learning &amp; Development',
          'url' => '#',
          'external' => false
        ),
        array(
          'title' => 'Statistics',
          'url' => '#',
          'external' => false
        ),
        array(
          'title' => 'Finances',
          'url' => '#',
          'external' => false
        ),
        array(
          'title' => 'Justice academy',
          'url' => '#',
          'external' => false
        )
      )
    );
  }
}

new Page_home();
