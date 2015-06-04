<?php

/**
 *
 * Creates news dashboard to allow admins to customise news on home page
 *
 */

  if (!defined('ABSPATH')) {
    exit; // disable direct access
  }

  if (!class_exists('NewsCustomiser')) {

    class NewsCustomiser {

      public function NewsCustomiser() {
        add_action( 'admin_enqueue_scripts', array( &$this,'load_autocomplete') );
        add_action( 'customize_register' , array( &$this , 'register' ) );

        // Action to promote news to featured on save
        add_action( 'save_post', array(&$this,'news_save'), 10, 3 );
      }

      public function load_autocomplete() {
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('customizer',get_template_directory_uri()."/js/customizer.js",array('jquery-ui-datepicker'),null,true);
        wp_enqueue_style('jquery-admin-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/overcast/jquery-ui.css', false, 0.1, false);
      }

      public function register($wp_customize) {
        $wp_customize->add_panel( 'news_customisation', array(
          'priority'        => 10,
          'capability'      => 'edit_theme_options',
          'title'           => 'Homepage News',
          'description'     => 'Allows admins and editors to customise which news is displayed on the homepage',
        ) );

        $this->featured_news($wp_customize,2);
        $this->need_to_know($wp_customize,3);
        $this->emergency_message($wp_customize);
        $this->clean_up_customizer($wp_customize);
      }

      // Featured news functions

      public function featured_news($wp_customize,$total_stories = 2) {
        $wp_customize->add_section( 'featured_news', array(
          'priority'        => 10,
          'capability'      => 'edit_theme_options',
          'title'           => 'Featured news',
          'description'     => 'Controls the featured news items',
          'panel'           => 'news_customisation',
        ) );

        for($x=1;$x<=$total_stories;$x++) {
          $wp_customize->add_setting( 'featured_story'.$x, array(
            'type'      => 'option',
            'priority'  => 10,
            'section'   => 'featured_news',
            'label'     => 'Featured story '.$x,
            'transport' => 'refresh',
          ) );

          $wp_customize->add_control( new News_Dropdown_Custom_Control($wp_customize, 'featured_story'.$x.'_control', array (
            'label'     =>  'Featured story '.$x,
            'section'   =>  'featured_news',
            'settings'  =>  'featured_story'.$x
          )  )  );
        }
      }

      // Need to know functions

      public function need_to_know($wp_customize,$total_stories = 3) {
        // $wp_customize->add_control( new Heading_Custom_Control($wp_customize, 'need_to_know_heading'.$x.'_control', array (
        //   'label'     =>  'Need to know story '.$x,
        //   'section'   =>  'need_to_know'
        // )  )  );

        $wp_customize->add_section( 'need_to_know', array(
          'priority'        => 10,
          'capability'      => 'edit_theme_options',
          'title'           => 'Need to know',
          'description'     => 'Controls the "Need to know" items',
          'panel'           => 'news_customisation',
        ) );

        for($x=1;$x<=$total_stories;$x++) {
          $wp_customize->add_setting( 'need_to_know_story'.$x, array(
            'type'      => 'option',
            'priority'  => 10,
            'section'   => 'need_to_know',
            'label'     => 'Need to know story '.$x,
            'transport' => 'refresh',
          ) );

          $wp_customize->add_control( new News_Dropdown_Custom_Control($wp_customize, 'need_to_know_story'.$x.'_control', array (
            'label'     =>  'Need to know story '.$x,
            'section'   =>  'need_to_know',
            'settings'  =>  'need_to_know_story'.$x
          )  )  );

          $wp_customize->add_setting( 'need_to_know_tab'.$x, array(
            'type'      => 'option',
            'priority'  => 10,
            'section'   => 'need_to_know',
            'label'     => 'Need to know tab '.$x,
            'transport' => 'refresh',
          ) );

          $wp_customize->add_control( new WP_Customize_Control($wp_customize, 'need_to_know_tab'.$x, array (
            'label'     =>  'Need to know tab '.$x,
            'section'   =>  'need_to_know',
            'settings'  =>  'need_to_know_tab'.$x,
            'type'      =>  'text'
          )  )  );
        }
      }

      // Emergency message functions

      public function emergency_message($wp_customize) {
        $wp_customize->add_section( 'emergency_message_section', array(
          'priority'        => 10,
          'capability'      => 'edit_theme_options',
          'title'           => 'Notification message',
          'description'     => 'Controls the emergency message banner',
          'panel'           => 'news_customisation',
        ) );

        $wp_customize->add_setting( 'emergency_toggle', array(
          'type'      => 'option',
          'priority'  => 10,
          'section'   => 'emergency_message_section',
          'label'     => 'Enable Notification',
          'transport' => 'refresh',
        ) );

        $wp_customize->add_control( new WP_Customize_Control($wp_customize, 'emergency_toggle_control', array (
          'label'     =>  'Enable notification',
          'section'   =>  'emergency_message_section',
          'settings'  =>  'emergency_toggle',
          'type'      =>  'checkbox'
        )  )  );

        $wp_customize->add_setting( 'emergency_title', array(
          'type'      => 'option',
          'priority'  => 10,
          'section'   => 'emergency_message_section',
          'label'     => 'Notification Title',
          'transport' => 'refresh',
        ) );

        $wp_customize->add_control( new WP_Customize_Control($wp_customize, 'emergency_title_control', array (
          'label'     =>  'Notification Title',
          'section'   =>  'emergency_message_section',
          'settings'  =>  'emergency_title',
          'type'      =>  'text'
        )  )  );

        $wp_customize->add_setting( 'homepage_control_emergency_message', array(
          'type'      => 'option',
          'priority'  => 10,
          'section'   => 'emergency_message_section',
          'label'     => 'Notification Message',
          'transport' => 'refresh',
        ) );

        $wp_customize->add_control( new WP_Customize_Control($wp_customize, 'emergency_message_control', array (
          'label'     =>  'Notification Message',
          'section'   =>  'emergency_message_section',
          'settings'  =>  'homepage_control_emergency_message',
          'type'      =>  'textarea'
        )  )  );

        $wp_customize->add_setting( 'emergency_date', array(
          'type'      => 'option',
          'priority'  => 10,
          'section'   => 'emergency_message_section',
          'label'     => 'Notification Date',
          'transport' => 'refresh',
        ) );

        $wp_customize->add_control( new WP_Customize_Control($wp_customize, 'emergency_date_control', array (
          'label'     =>  'Notification Date',
          'section'   =>  'emergency_message_section',
          'settings'  =>  'emergency_date',
          'type'      =>  'text'
        )  )  );

        $wp_customize->add_setting( 'emergency_type', array(
          'type'      =>  'option',
          'priority'  =>  10,
          'section'   =>  'emergency_message_section',
          'label'     =>  'Notification Type',
          'transport' =>  'refresh',
          'default'   =>  'emergency'
        ) );

        $wp_customize->add_control( new WP_Customize_Control($wp_customize, 'emergency_type_control', array (
          'label'     =>  'Notification Type',
          'section'   =>  'emergency_message_section',
          'settings'  =>  'emergency_type',
          'type'      =>  'radio',
          'choices'   =>  array(
            'emergency'       =>  __('Emergency'),
            'service-update'  =>  __('Service update')
          )
        )  )  );

      }

      // Auto-promotes news on save

      public function news_save($post_id, $post, $update) {
        // If this is just a revision or is not news, don't do anything.
        if ( wp_is_post_revision( $post_id ) || $post->post_type != 'news')
          return;

        if ( isset( $_REQUEST['pods_meta_news_listing_type'] ) ) {
          $type = $_REQUEST['pods_meta_news_listing_type'];
          switch ($type) {
            case 2:
              $featured_story1 = $post_id;
              $featured_story2 = get_option('featured_story1');
              update_option( 'featured_story1', $featured_story1 );
              update_option( 'featured_story2', $featured_story2 );
              break;
            case 1:
              $need_to_know_story1 = $post_id;
              $need_to_know_story2 = get_option('need_to_know_story1');
              $need_to_know_story3 = get_option('need_to_know_story2');
              update_option( 'need_to_know_story1', $need_to_know_story1 );
              update_option( 'need_to_know_story2', $need_to_know_story2 );
              update_option( 'need_to_know_story3', $need_to_know_story3 );
            default:
              break;
          }
        }
      }

      // Remove unecessary customizer sections
      function clean_up_customizer($wp_customize) {
        if ( !current_user_can( 'manage_options' ) ) {
          $wp_customize->remove_section( 'title_tagline');
          $wp_customize->remove_section( 'colors');
          $wp_customize->remove_section( 'header_image');
          $wp_customize->remove_section( 'background_image');
          $wp_customize->remove_section( 'nav');
          $wp_customize->remove_section( 'static_front_page');
          $wp_customize->remove_panel( 'widgets');
        }
      }

    }

  }

global $wp_customize;
if ( isset( $wp_customize ) ) {
  $news_customiser = new NewsCustomiser;
}
