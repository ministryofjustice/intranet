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
      }

      public function load_autocomplete() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_style('jquery-admin-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/overcast/jquery-ui.css', false, 0.1, false);
      }

      public function register($wp_customize) {
        $wp_customize->add_panel( 'news_customisation', array(
          'priority'        => 10,
          'capability'      => 'edit_theme_options',
          'theme_supports'  => '',
          'title'           => 'Homepage News',
          'description'     => 'Allows admins and editors to customise which news is displayed on the homepage',
        ) );

        $wp_customize->add_section( 'featured_news', array(
          'priority'        => 10,
          'capability'      => 'edit_theme_options',
          'theme_supports'  => '',
          'title'           => 'Featured news',
          'description'     => 'Controls the featured news items',
          'panel'           => 'news_customisation',
        ) );

        $wp_customize->add_setting( 'first_story', array(
          'type'      => 'option',
          'priority'  => 10,
          'section'   => 'featured_news',
          'label'     => 'First featured story',
          'transport' => 'refresh',
        ) );

        $wp_customize->add_control( new News_Dropdown_Custom_Control($wp_customize, 'first_story_control', array (
          'label'     =>  'First featured story',
          'section'   =>  'featured_news',
          'settings'  =>  'first_story'
        )  )  );

        $wp_customize->add_setting( 'second_story', array(
          'type'      => 'option',
          'priority'  => 10,
          'section'   => 'featured_news',
          'label'     => 'Second featured story',
          'transport' => 'refresh',
        ) );

        $wp_customize->add_control( new News_Dropdown_Custom_Control($wp_customize, 'second_story_control', array (
          'label'     =>  'Second featured story',
          'section'   =>  'featured_news',
          'settings'  =>  'second_story'
        )  )  );

        // Remove unecessary customizer sections
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

  $news_customiser = new NewsCustomiser;