<?php

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override twentyten_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Twenty Ten 1.0
 * @uses register_sidebar
 */
function twentyten_widgets_init() {

  register_sidebar( array(
    'name' => __( 'Homepage first column', 'twentyten' ),
    'id' => 'home-widget-area0',
    'description' => __( 'Homepage 1st column', 'twentyten' ),
    'before_widget' => '<div class="category-block">',
    'after_widget' => '</div>',
    'before_title' => '<h3>',
    'after_title' => '</h3>',
  ) );
  register_sidebar( array(
    'name' => __( 'Left footer', 'twentyten' ),
    'id' => 'first-footer-widget-area',
    'description' => __( 'The main footer widget area', 'twentyten' ),
    'before_widget' => '<div class="widget-box">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>',
  ) );
  register_sidebar( array(
    'name' => __( 'News landing page area 0', 'twentyten' ),
    'id' => 'newslanding-widget-area0',
    'description' => __( 'The top area on the news page', 'twentyten' ),
    'before_widget' => '<div>',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>',
  ) );
  register_sidebar( array(
    'name' => __( 'Guidance - Index', 'twentyten' ),
    'id' => 'guidance-index',
    'description' => __( 'Guidance - Index', 'twentyten' ),
    'before_widget' => '',
    'after_widget' => '',
    'before_title' => '',
    'after_title' => '',
  ) );
  register_sidebar( array(
    'name' => __( 'Main menu', 'twentyten' ),
    'id' => 'main-menu',
    'description' => __( 'Main menu', 'twentyten' ),
    'before_widget' => '',
    'after_widget' => '',
    'before_title' => '',
    'after_title' => '',
  ) );
}

/** Register sidebars by running twentyten_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'twentyten_widgets_init' );