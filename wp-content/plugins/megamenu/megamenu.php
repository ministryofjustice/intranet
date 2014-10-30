<?php

/*
 * Plugin Name: Max Mega Menu
 * Plugin URI:  http://www.megamenu.co.uk
 * Description: Mega Menu for WordPress.
 * Version:     1.3.2
 * Author:      Tom Hemsley
 * Author URI:  http://www.megamenu.co.uk
 * License:     GPL-2.0+
 * Copyright:   2014 Tom Hemsley
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

if ( ! class_exists( 'Mega_Menu' ) ) :

/**
 * Main plugin class
 */
final class Mega_Menu {


	/**
	 * @var string
	 */
	public $version = '1.3.2';


	/**
	 * Init
	 *
	 * @since 1.0
	 */
	public static function init() {
		$plugin = new self();
	}


	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();

		add_filter( 'wp_nav_menu_args', array( $this, 'modify_nav_menu_args' ), 9999 );
		add_filter( 'wp_nav_menu', array( $this, 'add_responsive_toggle' ), 10, 2 );
		add_filter( 'megamenu_nav_menu_css_class', array( $this, 'prefix_menu_classes' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'wp_nav_menu_objects', array( $this, 'add_widgets_to_menu' ), 10, 2 );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

        add_action( 'megamenu_after_save_settings', array( $this, 'clear_caches' ) );
        add_action( 'megamenu_after_save_settings', array( $this, 'regenerate_css' ) );

        add_action( 'megamenu_after_widget_add', array( $this, 'clear_caches' ) );
        add_action( 'megamenu_after_widget_save', array( $this, 'clear_caches' ) );
        add_action( 'megamenu_after_widget_delete', array( $this, 'clear_caches' ) );

		add_action( 'megamenu_after_theme_save', array( $this, 'regenerate_css') );
		add_action( 'megamenu_after_theme_delete', array( $this, 'regenerate_css') );
		add_action( 'megamenu_after_theme_revert', array( $this, 'regenerate_css') );
		add_action( 'megamenu_after_theme_duplicate', array( $this, 'regenerate_css') );
		add_action( 'megamenu_after_theme_create', array( $this, 'regenerate_css') );

		add_action( 'megamenu_after_install', array( $this, 'record_version_number') );
		add_action( 'megamenu_after_install', array( $this, 'regenerate_css') );
		add_action( 'megamenu_after_update', array( $this, 'record_version_number') );
		add_action( 'megamenu_after_update', array( $this, 'regenerate_css') );

		register_deactivation_hook( __FILE__, array( $this, 'delete_version_number') );

		add_shortcode( 'maxmenu', array( $this, 'register_shortcode' ) );
		
		add_action( 'after_switch_theme', array( $this, 'regenerate_css') );	

		if ( is_admin() ) {

			new Mega_Menu_Nav_Menus();
			new Mega_Menu_Widget_Manager();
			new Mega_Menu_Theme_Editor();

			$this->install_upgrade_check();

		}

		$mega_menu_style_manager = new Mega_Menu_Style_Manager();
		$mega_menu_style_manager->setup_actions();

	}


	/**
	 * Detect new or updated installations and run actions accordingly.
	 * 
	 * @since 1.3
	 */
	public function install_upgrade_check() {

		if ( $version = get_option( "megamenu_version" ) ) {

			if ( version_compare( $this->version, $version, '>' ) ) {

				do_action( "megamenu_after_update" );
				
			}

		} else {

			do_action( "megamenu_after_install" );

		}		

	}


	/**
	 * Store the current version number
	 *
	 * @since 1.3
	 */
	public function record_version_number() {

		if ( get_option( "megamenu_version" ) ) {

			update_option( "megamenu_version", $this->version );

		} else {

			add_option( "megamenu_version", $this->version );

		}
		
	}

	/**
	 * Store the current version number
	 *
	 * @since 1.3
	 */
	public function delete_version_number() {

		delete_option( "megamenu_version", $this->version );
		
	}


    /**
     * Shortcode used to display a menu
     *
     * @since 1.3
     * @return string
     */
    public function register_shortcode( $atts ) {

        if ( ! isset( $atts['location'] ) ) {
            return false;
        }

        if ( has_nav_menu( $atts['location'] ) ) {
		     return wp_nav_menu( array( 'theme_location' => $atts['location'], 'echo' => false ) );
		}

        return "<!-- menu not found [maxmenu location={$atts['location']}] -->";

    }


    /**
     * Initialise translations
	 *
	 * @since 1.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( 'megamenu', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }


	/**
	 * Define Mega Menu constants
	 *
	 * @since 1.0
	 */
	private function define_constants() {

		define( 'MEGAMENU_VERSION',    $this->version );
		define( 'MEGAMENU_BASE_URL',   trailingslashit( plugins_url( 'megamenu' ) ) );
		define( 'MEGAMENU_PATH',       plugin_dir_path( __FILE__ ) );

	}


	/**
	 * All Mega Menu classes
	 *
	 * @since 1.0
	 */
	private function plugin_classes() {

		return array(
			'mega_menu_walker'         => MEGAMENU_PATH . 'classes/walker.class.php',
			'mega_menu_widget_manager' => MEGAMENU_PATH . 'classes/widget-manager.class.php',
			'mega_menu_nav_menus'      => MEGAMENU_PATH . 'classes/nav-menus.class.php',
			'mega_menu_style_manager'  => MEGAMENU_PATH . 'classes/style-manager.class.php',
			'mega_menu_theme_editor'   => MEGAMENU_PATH . 'classes/theme-editor.class.php',
			'scssc'                    => MEGAMENU_PATH . 'classes/scssc.inc.php',

		);

	}


	/**
	 * Load required classes
	 *
	 * @since 1.0
	 */
	private function includes() {

		$autoload_is_disabled = defined( 'MEGAMENU_AUTOLOAD_CLASSES' ) && MEGAMENU_AUTOLOAD_CLASSES === false;

		if ( function_exists( "spl_autoload_register" ) && ! ( $autoload_is_disabled ) ) {

			// >= PHP 5.2 - Use auto loading
			if ( function_exists( "__autoload" ) ) {
				spl_autoload_register( "__autoload" );
			}

			spl_autoload_register( array( $this, 'autoload' ) );

		} else {

			// < PHP5.2 - Require all classes
			foreach ( $this->plugin_classes() as $id => $path ) {
				if ( is_readable( $path ) && ! class_exists( $id ) ) {
					require_once $path;
				}
			}

		}

	}


	/**
	 * Autoload classes to reduce memory consumption
	 *
	 * @since 1.0
	 * @param string $class
	 */
	public function autoload( $class ) {

		$classes = $this->plugin_classes();

		$class_name = strtolower( $class );

		if ( isset( $classes[ $class_name ] ) && is_readable( $classes[ $class_name ] ) ) {
			require_once $classes[ $class_name ];
		}

	}


	/**
	 * Appends "mega-" to all menu classes.
	 * This is to help avoid theme CSS conflicts.
	 *
	 * @since 1.0
	 * @param array $classes
	 * @return array
	 */
	public function prefix_menu_classes( $classes ) {
		$return = array();

		foreach ( $classes as $class ) {
			$return[] = 'mega-' . $class;
		}

		return $return;
	}


	/** 
	 * Add the html for the responsive toggle box to the menu
	 *
	 * @param string $nav_menu
	 * @param object $args
	 * @return string
	 * @since 1.3
	 */
	public function add_responsive_toggle( $nav_menu, $args ) {

		// make sure we're working with a Mega Menu
		if ( ! is_a( $args->walker, 'Mega_Menu_Walker' ) )
			return $nav_menu;
		
		$toggle_id = 'mega-menu-toggle-' . $args->theme_location . '-' . $args->menu;

		$toggle_class = 'mega-menu-toggle';

		$find = 'class="' . $args->container_class . '">';

		$replace = $find . '<input type="checkbox" id="' . $toggle_id . '" class="' . $toggle_class . '">
							<label for="'. $toggle_id . '"></label>';

		return str_replace( $find, $replace, $nav_menu );
	}


   	/**
   	 * Append the widget objects to the menu array before the 
   	 * menu is processed by the walker.
   	 *
   	 * @since 1.0
   	 * @param array $items - All menu item objects
	 * @param object $args
	 * @return array - Menu objects including widgets
   	 */
	public function add_widgets_to_menu( $items, $args ) {

		// make sure we're working with a Mega Menu
		if ( ! is_a( $args->walker, 'Mega_Menu_Walker' ) )
			return $items;

		$widget_manager = new Mega_Menu_Widget_Manager();

		$default_columns = apply_filters("megamenu_default_columns", 1);

		foreach ( $items as $item ) {

			// only look for widgets on top level items
			if ( $item->menu_item_parent == 0 ) {

				$settings = get_post_meta( $item->ID, '_megamenu', true );

		        $align_class = isset( $settings['align'] ) ? 'align-' . $settings['align'] : 'align-bottom-left';
		        $type_class = isset( $settings['type'] ) ? 'menu-' . $settings['type'] : 'menu-flyout';

		        $item->classes[] = $align_class;
		        $item->classes[] = $type_class;

				if ( $type_class == 'menu-megamenu' ) {

					$panel_widgets = $widget_manager->get_widgets_for_menu_id( $item->ID );

					if ( count( $panel_widgets) ) {

						if ( ! in_array( 'menu-item-has-children', $item->classes ) ) {
							$item->classes[] = 'menu-item-has-children';
						}

						$cols = 0;

						foreach ( $panel_widgets as $widget ) {

							$menu_item = array(
								'type'             => 'widget',
								'title'            => '',
								'content'          => $widget_manager->show_widget( $widget['widget_id'] ),
								'menu_item_parent' => $item->ID,
								'db_id'            => 0, // This menu item does not have any childen
								'ID'               => $widget['widget_id'],
								'classes'          => array(
									"menu-item", 
									"menu-item-type-widget", 
									"menu-columns-{$widget['mega_columns']}"
								)
							);

							if ( $cols == 0 ) {
								$menu_item['classes'][] = "menu-clear";
							}

							$cols = $cols + $widget['mega_columns'];

							if ( $cols > 6 ) {
								$menu_item['classes'][] = "menu-clear";
								$cols = $widget['mega_columns'];
							}

							$items[] = (object) $menu_item;
						}
					}
				}
			} else {
				$item->classes[] = "menu-columns-{$default_columns}";
			}
		}

		return $items;    
	}


	/**
	 * Use the Mega Menu walker to output the menu
	 * Resets all parameters used in the wp_nav_menu call
	 * Wraps the menu in mega-menu IDs and classes
	 *
	 * @since 1.0
	 * @param $args array
	 * @return array
	 */
	public function modify_nav_menu_args( $args ) {

		$settings = get_site_option( 'megamenu_settings' );

		$current_theme_location = $args['theme_location'];

		$locations = get_nav_menu_locations();

		if ( isset ( $settings[ $current_theme_location ]['enabled'] ) && $settings[ $current_theme_location ]['enabled'] == true ) {

			$menu_id = $locations[ $current_theme_location ];

			if ( ! $menu_id ) {
				return $args;
			}

			$menu_settings = $settings[ $current_theme_location ];
			$mega_menu_layout = isset( $menu_settings['layout'] ) ? $menu_settings['layout'] : 'horizontal';
			$event = isset( $menu_settings['event'] ) ? $menu_settings['event'] : 'hover';
			$effect = isset( $menu_settings['effect'] ) ? $menu_settings['effect'] : 'disabled';

			$wrap_attributes = apply_filters("megamenu_wrap_attributes", array(
				"id" => '%1$s',
				"class" => '%2$s mega-no-js',
				"data-event" => $event,
				"data-effect" => $effect
			), $menu_id, $menu_settings );

			$attributes = "";

			foreach( $wrap_attributes as $attribute => $value ) {
				$attributes .= " " . $attribute . '="' . $value . '"';
			}

			$defaults = array(
				'menu'            => $menu_id,
				'container'       => 'div',
				'container_class' => 'mega-menu-wrap',
				'container_id'    => 'mega-menu-wrap-' . $current_theme_location . '-' . $menu_id,
				'menu_class'      => 'mega-menu mega-menu-' . $mega_menu_layout,
				'menu_id'         => 'mega-menu-' . $current_theme_location . '-' . $menu_id,
				'fallback_cb'     => 'wp_page_menu',
				'before'          => '',
				'after'           => '',
				'link_before'     => '',
				'link_after'      => '',
				'items_wrap'      => '<ul' . $attributes . '>%3$s</ul>',
				'depth'           => 0,
				'walker'          => new Mega_Menu_Walker()
			);

			$args = array_merge( $args, $defaults );
		}

		return $args;
	}


	/**
	 * Display admin notices.
	 */
	public function admin_notices() {

		if ( ! $this->is_compatible_wordpress_version() ) :

	    ?>
	    <div class="error">
	        <p><?php _e( 'MaxMenu is not compatible with your version of WordPress. Please upgrade WordPress to the latest version or disable Mega Menu.', 'megamenu' ); ?></p>
	    </div>
	    <?php

	    endif;


		if ( is_plugin_active('ubermenu/ubermenu.php') ) :

	    ?>
	    <div class="error">
	        <p><?php _e( 'MaxMenu is not compatible with Uber Menu. Please disable Uber Menu.', 'megamenu' ); ?></p>
	    </div>
	    <?php

	    endif;


		if ( did_action('megamenu_after_install') === 1 ) :

	    ?>
	    <div class="updated">
	        <p><?php _e( 'Thanks for installing MaxMenu! Please head to Appearance > Menus to get started.', 'megamenu' ); ?></p>
	    </div>
	    <?php

	    endif;
	}


	/**
	 * Checks this WordPress installation is v3.8 or above.
	 * 3.8 is needed for dashicons.
	 */
	public function is_compatible_wordpress_version() {
		global $wp_version;

		return $wp_version >= 3.8;
	}


    /**
     * Clear the cache when the Mega Menu is updated.
     *
     * @since 1.0
     */
    public function clear_caches() {

        // https://wordpress.org/plugins/widget-output-cache/
        if ( function_exists( 'menu_output_cache_bump' ) ) {
            menu_output_cache_bump();
        }

        // https://wordpress.org/plugins/widget-output-cache/
        if ( function_exists( 'widget_output_cache_bump' ) ) {
            widget_output_cache_bump();
        }

        // https://wordpress.org/plugins/wp-super-cache/
        if ( function_exists( 'wp_cache_clear_cache' ) ) {
            global $wpdb;
            wp_cache_clear_cache( $wpdb->blogid );
        }

    }


    /**
     * Regenerate the CSS for the menu's. The generated CSS is then cached.
     *
     * @since 1.2
     */
    public function regenerate_css() {
        $style_manager = new Mega_Menu_Style_Manager();
        $style_manager->empty_cache();
    }
}

add_action( 'plugins_loaded', array( 'Mega_Menu', 'init' ), 10 );

endif;