<?php

/**
 * Creates news dashboard to allow admins to customise news on home page
 */

  if (!defined('ABSPATH')) {
    exit; // disable direct access
  }

  if (!class_exists('NewsCustomiser')) {

    class NewsCustomiser {

      public function __construct() {
        add_action( 'admin_enqueue_scripts', array( &$this,'load_autocomplete') );
        add_action( 'customize_register' , array( &$this , 'register' ), 20 );
      }

      public function load_autocomplete() {
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('customizer',get_template_directory_uri()."/admin/js/customizer.js",array('jquery-ui-datepicker'),null,true);
        wp_enqueue_style('jquery-admin-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/overcast/jquery-ui.css', false, 0.1, false);
        wp_enqueue_style('intranet-customizer-css', get_template_directory_uri()."/admin/css/customizer.css", array('jquery-admin-ui-css'));
      }

      public function register($wp_customize) {

        $context = Agency_Context::get_agency_context();
        $agency = Agency_Editor::get_agency_by_slug($context);

        $wp_customize->add_panel( 'need_to_know_customisation', array(
          'priority'        => 11,
          'capability'      => 'edit_theme_options',
          'title'           => 'Need To Know for ' . $agency->name,
          'description'     => 'Allows admins and editors to customise the Need To Know panel',
        ) );
        
        //$this->homepage_banner($wp_customize);
        $this->featured_items($wp_customize,2);
        $this->need_to_know($wp_customize,3);
        $this->emergency_message($wp_customize);
        $this->clean_up_customizer($wp_customize);
      }

      public function homepage_banner($wp_customize) {
        $context = Agency_Context::get_agency_context();
        $agency = Agency_Editor::get_agency_by_slug($context);

        $section_name = 'homepage_banner';
        $wp_customize->add_section( 'homepage_banner', array(
            'priority'        => 10,
            'capability'      => 'edit_theme_options',
            'title'           => 'Homepage Banner for ' . $agency->name
        ) );

        $this->new_control_setting($wp_customize, $context.'_banner_image', $section_name, 'Image', 'image');
        $this->new_control_setting($wp_customize, $context.'_banner_link', $section_name, 'Link', 'text');

      }

      // Featured items functions
      public function featured_items($wp_customize,$total_stories = 2) {

        $context = Agency_Context::get_agency_context();
        $agency = Agency_Editor::get_agency_by_slug($context);

        $section_name = 'featured_news';
        $wp_customize->add_section( 'featured_news', array(
          'priority'        => 10,
          'capability'      => 'edit_theme_options',
          'title'           => 'Featured items for ' . $agency->name
        ) );



        for($x=1;$x<=$total_stories;$x++) {


          $this->new_control_setting($wp_customize, $context.'_featured_story'.$x, $section_name, 'Featured item ' . $x, 'content_dd');
        }
      }

      // Need to know functions
      public function need_to_know($wp_customize,$total_stories = 3) {

        $context = Agency_Context::get_agency_context();

        for($x=1;$x<=$total_stories;$x++) {
          $section_name = 'need_to_know' . $x;
          $wp_customize->add_section( $section_name, array(
            'capability'      => 'edit_theme_options',
            'title'           => 'Slide ' . $x,
            'panel'           => 'need_to_know_customisation',
          ) );

          $this->new_control_setting($wp_customize, $context.'_need_to_know_headline'.$x, $section_name, 'Headline', 'text');
          $this->new_control_setting($wp_customize, $context.'_need_to_know_url'.$x, $section_name, 'URL', 'text');
          $this->new_control_setting($wp_customize, $context.'_need_to_know_image'.$x, $section_name, 'Image', 'image');
          $this->new_control_setting($wp_customize, $context.'_need_to_know_alt'.$x, $section_name, 'Image alt text', 'text');
        }
      }

      // Emergency message functions

      public function emergency_message($wp_customize) {

        $context = Agency_Context::get_agency_context();
        $agency = Agency_Editor::get_agency_by_slug($context);

        $section_name = 'emergency_message_section';
        $wp_customize->add_section( 'emergency_message_section', array(
          'priority'        => 1,
          'capability'      => 'edit_theme_options',
          'title'           => 'Notification message for ' . $agency->name
        ) );

        $this->new_control_setting($wp_customize, $context.'_emergency_toggle', $section_name, 'Enable Notification', 'checkbox');
        $this->new_control_setting($wp_customize, $context.'_emergency_title', $section_name, 'Notification Title', 'text');
        $this->new_control_setting($wp_customize, $context.'_homepage_control_emergency_message', $section_name, 'Notification Message', 'textarea');
        $this->new_control_setting($wp_customize, $context.'_emergency_date', $section_name, 'Notification Date', 'text');
        $this->new_control_setting($wp_customize, $context.'_emergency_type', $section_name, 'Notification Type', 'radio', array(
          'default' => 'emergency'
        ),array(
          'choices'   =>  array(
            'emergency'       =>  __('Emergency'),
            'service-update'  =>  __('Service update')
          )
        ));
      }

      private function new_control_setting($wp_customize,$name,$section,$label,$type,$setting_params = array(),$control_params = array()) {
        // Set control class
        $control_classes = array(
          'content_dd'  => 'Content_Dropdown_Custom_Control',
          'image' => 'WP_Customize_Image_Control'
        );

        $control_class = (key_exists($type, $control_classes)) ? $control_classes[$type] : 'WP_Customize_Control';

        $wp_customize->add_setting($name, array_merge(array(
          'type'     => 'option',
          'section'  => $section,
          'label'    => $label
        ),$setting_params));

        $wp_customize->add_control(new $control_class($wp_customize, $name, array_merge(array (
          'type'     => $type,
          'label'    => $label,
          'section'  => $section,
          'settings' => $name
        ),$control_params)));
      }

      // Remove unecessary customizer sections
      function clean_up_customizer($wp_customize) {
        if ( !current_user_can( 'manage_options' ) ) {
          $wp_customize->remove_panel( 'widgets');
          $wp_customize->remove_panel( 'nav_menus');
        }
      }

    }

  }

global $wp_customize;
if ( isset( $wp_customize ) ) {
  $news_customiser = new NewsCustomiser;
}
