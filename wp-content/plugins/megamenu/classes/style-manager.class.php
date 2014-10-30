<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

if ( ! class_exists( 'Mega_Menu_Style_Manager' ) ) :

/**
 * 
 */
final class Mega_Menu_Style_Manager {

	/**
	 * Constructor
     *
     * @since 1.0
	 */
	public function __construct() {

	}


	/**
	 * Setup actions
	 *
	 * @since 1.0
	 */
	public function setup_actions() {

		add_action( 'wp_ajax_megamenu_css', array( $this, 'get_css') );
		add_action( 'wp_ajax_nopriv_megamenu_css', array( $this, 'get_css') );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_print_styles', array( $this, 'head_css' ), 9999 );

	}


    /**
     *
     *
     * @since 1.0
     */
    public function default_themes() {

        $themes['default'] = array(
            'title'                                     => __("Default", "megamenu"),
            'container_background_from'                 => '#222',
            'container_background_to'                   => '#222',
            'container_padding_left'                    => '0px',
            'container_padding_right'                   => '0px',
            'container_padding_top'                     => '0px',
            'container_padding_bottom'                  => '0px',
            'container_border_radius_top_left'          => '0px',
            'container_border_radius_top_right'         => '0px',
            'container_border_radius_bottom_left'       => '0px',
            'container_border_radius_bottom_right'      => '0px',
            'arrow_up'                                  => 'dash-f142',
            'arrow_down'                                => 'dash-f140',
            'arrow_left'                                => 'dash-f141',
            'arrow_right'                               => 'dash-f139',
            'menu_item_background_from'                 => 'transparent',
            'menu_item_background_to'                   => 'transparent',
            'menu_item_background_hover_from'           => '#333',
            'menu_item_background_hover_to'             => '#333',
            'menu_item_spacing'                         => '0px',
            'menu_item_link_font'                       => 'inherit',
            'menu_item_link_font_size'                  => '14px',
            'menu_item_link_height'                     => '40px',
            'menu_item_link_color'                      => '#ffffff',
            'menu_item_link_weight'                     => 'normal',
            'menu_item_link_text_transform'             => 'normal',
            'menu_item_link_color_hover'                => '#ffffff',
            'menu_item_link_weight_hover'               => 'normal',
            'menu_item_link_padding_left'               => '10px',
            'menu_item_link_padding_right'              => '10px',
            'menu_item_link_padding_top'                => '0px',
            'menu_item_link_padding_bottom'             => '0px',
            'menu_item_link_border_radius_top_left'     => '0px',
            'menu_item_link_border_radius_top_right'    => '0px',
            'menu_item_link_border_radius_bottom_left'  => '0px',
            'menu_item_link_border_radius_bottom_right' => '0px',
            'panel_background_from'                     => '#f1f1f1',
            'panel_background_to'                       => '#f1f1f1',
            'panel_width'                               => '100%',
			'panel_border_color'                        => '#fff',
            'panel_border_left'                         => '0px',
            'panel_border_right'                        => '0px',
            'panel_border_top'                          => '0px',
            'panel_border_bottom'                       => '0px',
            'panel_border_radius_top_left'              => '0px',
            'panel_border_radius_top_right'             => '0px',
            'panel_border_radius_bottom_left'           => '0px',
            'panel_border_radius_bottom_right'          => '0px',
            'panel_header_color'                        => '#555',
            'panel_header_text_transform'               => 'uppercase',
            'panel_header_font'                         => 'inherit',
            'panel_header_font_size'                    => '16px',
            'panel_header_font_weight'                  => 'bold',
            'panel_header_padding_top'                  => '0px',
            'panel_header_padding_right'                => '0px',
            'panel_header_padding_bottom'               => '5px',
            'panel_header_padding_left'                 => '0px',
            'panel_padding_left'                        => '0px',
            'panel_padding_right'                       => '0px',
            'panel_padding_top'                         => '0px',
            'panel_padding_bottom'                      => '0px',
            'panel_widget_padding_left'                 => '15px',
            'panel_widget_padding_right'                => '15px',
            'panel_widget_padding_top'                  => '15px',
            'panel_widget_padding_bottom'               => '15px',
            'flyout_width'                              => '150px',
			'flyout_border_color'                        => '#ffffff',
            'flyout_border_left'                         => '0px',
            'flyout_border_right'                        => '0px',
            'flyout_border_top'                          => '0px',
            'flyout_border_bottom'                       => '0px',
            'flyout_link_padding_left'                  => '10px',
            'flyout_link_padding_right'                 => '10px',
            'flyout_link_padding_top'                   => '0px',
            'flyout_link_padding_bottom'                => '0px',
            'flyout_link_weight'                        => 'normal',
            'flyout_link_weight_hover'                  => 'normal',
            'flyout_link_height'                        => '35px',
            'flyout_background_from'                    => '#f1f1f1',
            'flyout_background_to'                      => '#f1f1f1',
            'flyout_background_hover_from'              => '#dddddd',
            'flyout_background_hover_to'                => '#dddddd',
            'font_size'                                 => '14px',
            'font_color'                                => '#666',
            'font_family'                               => 'inherit',
            'responsive_breakpoint'                     => '600px',
            'line_height'                               => '1.7',
            'z_index'                                   => '999',
            'custom_css'                                => '
#{$wrap} #{$menu} {
    /** Custom styles should be added below this line **/
}
#{$wrap} { 
    clear: both;
}'
        );

        return apply_filters( "megamenu_themes", $themes);
    }


    /**
     * Return a filtered list of themes
     *
     * @since 1.0
     * @return array
     */
    public function get_themes() {

    	$default_themes = $this->default_themes();

    	if ( $saved_themes = get_site_option( "megamenu_themes" ) ) {

    		foreach ( $default_themes as $key => $settings ) {

    			// Merge in any custom modifications to default themes
    			if ( isset( $saved_themes[ $key ] ) ) {

    				$default_themes[ $key ] = array_merge( $default_themes[ $key ], $saved_themes[ $key ] );
    				unset( $saved_themes[ $key ] );

    			}

    		}

    		foreach ( $saved_themes as $key => $settings ) {

    			// Add in saved themes, ensuring they always have a placeholder for any new settings
    			// which have since been added to the default theme.
    			$default_themes[ $key ] = array_merge ( $default_themes['default'], $settings );

    		}

    	}

		uasort( $default_themes, array( $this, 'sort_by_title' ) );

    	return $default_themes;
    	
    }


    /**
     * Sorts a 2d array by the 'title' key
     *
     * @since 1.0
     * @param array $a
     * @param array $b
     */
    function sort_by_title( $a, $b ) {

	    return strcmp( $a['title'], $b['title'] );
        
	}


    /**
     *
     * @since 1.3.1
     */
    private function is_debug_mode() {

        return ( defined( 'MEGAMENU_DEBUG' ) && MEGAMENU_DEBUG === true ) || isset( $_GET['nocache'] );

    }


	/**
	 * Return the menu CSS. Use the cache if possible.
     *
     * @since 1.0
	 */
	public function get_css() {

		header("Content-type: text/css; charset: UTF-8");

        if ( ( $css = get_site_transient('megamenu_css') ) && ! $this->is_debug_mode() ) {

			echo $css;
			echo "\n/** CSS served from cache **/";

		} else {

			echo $this->generate_css( 'scss_formatter' );

		}

	  	wp_die();
	}


    /**
     * Return the menu CSS for use in inline CSS block. Use the cache if possible.
     *
     * @since 1.3.1
     */
    public function get_inline_css() {


        if ( ( $css = get_site_transient('megamenu_css') ) && ! $this->is_debug_mode() ) {

            return $css . "\n/** CSS served from cache **/";

        } else {

            return $this->generate_css( 'scss_formatter_compressed' );

        }

    }


	/**
	 * Generate and cache the CSS for our menus.
	 * The CSS is compiled by lessphp using the file located in /css/megamenu.less
     *
     * @since 1.0
	 * @return string
	 * @param boolean $debug_mode (prints error messages to the CSS when enabled)
	 */
	public function generate_css( $scss_formatter = 'scss_formatter' ) {

		$start_time = microtime( true );

	  	$settings = get_site_option( "megamenu_settings" );

	  	if ( ! $settings ) {
	  		return "/** CSS Generation Failed. No menu settings found **/";
	  	}

  		$css = "";
        $exception = false;

	  	foreach ( $settings as $location => $settings ) {

            if ( ! isset( $settings['enabled'] ) ) {
                continue;
            }

            if ( ! has_nav_menu( $location ) ) {

                $exception = true;
                $css .= "/** Menu for location does not exist: {$location} **/";

            } else {

                $theme = $this->get_theme_settings_for_location( $location );
                $menu_id = $this->get_menu_id_for_location( $location );
                $compiled_css = $this->generate_css_for_location( $location, $theme, $menu_id, $scss_formatter );

    			if ( is_wp_error( $compiled_css ) ) {

                    $exception = true;
                    $css .= "/** Failed to compile CSS for location: {$location} **/";

    			} else {

                    $css .= $compiled_css;

                }
            }

	  	}

		$load_time = number_format( microtime(true) - $start_time, 4 );

        $css .= "\n/** Dynamic CSS generated in " . $load_time . " seconds **/";

	  	if ( ! $exception ) {

            $css .= "\n/** Cached CSS generated by MaxMenu on " . date('l jS \of F Y h:i:s A') . " **/";

	  		set_site_transient( 'megamenu_css', $css, 0 );

	  	} else {

            $this->empty_cache();

        }

	  	return $css;
	}


    /**
     * Empty the CSS cache
     *
     * @since 1.3
     */
    public function empty_cache() {

        delete_site_transient( 'megamenu_css' );

    }


	/**
	 * Compiles raw SCSS into CSS for a particular menu location.
     *
     * @since 1.3
     * @return mixed
     * @param array $settings
     * @param string $location
	 */
	public function generate_css_for_location( $location, $theme, $menu_id, $scss_formatter = 'scss_formatter' ) {

		$scssc = new scssc();
		$scssc->setFormatter( $scss_formatter );

        $import_paths = apply_filters('megamenu_scss_import_paths', array(
            trailingslashit( get_stylesheet_directory() ) . trailingslashit("megamenu"),
            trailingslashit( get_stylesheet_directory() ),
            trailingslashit( get_template_directory() ) . trailingslashit("megamenu"),
            trailingslashit( get_template_directory() ),
            trailingslashit( WP_PLUGIN_DIR )
        ));

        foreach ( $import_paths as $path ) {
            $scssc->addImportPath( $path );
        }

		try {
		    return $scssc->compile( $this->get_complete_scss_for_location( $location, $theme, $menu_id ) );
		}
		catch ( Exception $e ) {
			$message = __("Warning: CSS compilation failed. Please check your changes or revert the theme.", "megamenu");

			return new WP_Error( 'scss_compile_fail', $message . "<br /><br />" . $e->getMessage() );
		}

	}


    /**
     * Generates a SCSS string which includes the variables for a menu theme,
     * for a particular menu location.
     * 
     * @since 1.3
     * @return string
     * @param string $theme
     * @param string $location
     */
    private function get_complete_scss_for_location( $location, $theme, $menu_id ) {

        $scss = "\$wrap: \"#mega-menu-wrap-{$location}-{$menu_id}\";
                 \$menu: \"#mega-menu-{$location}-{$menu_id}\";
                 \$menu_id: \"{$menu_id}\";
                 \$number_of_columns: 6;";

        foreach( $theme as $name => $value ) {

            if ( in_array( $name, array( 'arrow_up', 'arrow_down', 'arrow_left', 'arrow_right' ) ) ) {

                $parts = explode( '-', $value );
                $code = end( $parts );

                $arrow_icon = $code == 'disabled' ? "''" : "'\\" . $code . "'";

                $scss .= "$" . $name . ": " . $arrow_icon . ";\n";

                continue;
            }

            if ( $name != 'custom_css' ) {
                $scss .= "$" . $name . ": " . $value . ";\n";
            }

        }

        $scss .= $this->load_scss_file();
        
        $scss .= stripslashes( html_entity_decode( $theme['custom_css'] ) );

        return apply_filters( "megamenu_scss", $scss, $location, $theme, $menu_id );

    }


    /**
     * Returns the menu ID for a specified menu location, defaults to 0
     * 
     * @since 1.3
     */
    private function get_menu_id_for_location( $location ) {
        
        $locations = get_nav_menu_locations();

        $menu_id = isset( $locations[ $location ] ) ? $locations[ $location ] : 0;

        return $menu_id;

    }


    /**
     * Returns the theme settings for a specified location. Defaults to the default theme.
     * 
     * @since 1.3
     */
    private function get_theme_settings_for_location( $location ) {

        $settings = get_site_option( "megamenu_settings" );

        $theme_id = isset( $settings[ $location ]['theme'] ) ? $settings[ $location ]['theme'] : 'default';

        $all_themes = $this->get_themes();

        $theme_settings = isset( $all_themes[ $theme_id ] ) ? $all_themes[ $theme_id ] : $all_themes[ 'default' ];

        return $theme_settings;

    }


	/**
	 * Return the path to the megamenu.scss file, look for custom files before
	 * loading the core version.
     *
     * @since 1.0
	 * @return string
	 */
	private function load_scss_file() {

		$locations = apply_filters( "megamenu_scss_locations", array(
			trailingslashit( get_stylesheet_directory() ) . trailingslashit("megamenu") . 'megamenu.scss', // child theme
			trailingslashit( get_template_directory() ) . trailingslashit("megamenu") . 'megamenu.scss', // parent theme
            MEGAMENU_PATH . trailingslashit('css') . 'megamenu.scss' // default
		));

 		foreach ( $locations as $path ) {

            if ( file_exists( $path ) ) {

                return file_get_contents( $path );

            }

 		}

        return false;
	}


	/**
	 * Enqueue public CSS and JS files required by Mega Menu
     *
     * @since 1.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'hoverIntent' );
		wp_enqueue_script( 'megamenu', MEGAMENU_BASE_URL . "js/public.js", array('jquery', 'hoverIntent'), MEGAMENU_VERSION );

		$params = apply_filters("megamenu_javascript_localisation", 
			array( 
				'fade_speed' => 'fast',
				'slide_speed' => 'fast',
                'timeout' => 300
			)
		);

		wp_localize_script( 'megamenu', 'megamenu', $params );

		wp_enqueue_style( 'megamenu', admin_url('admin-ajax.php') . '?action=megamenu_css', false, MEGAMENU_VERSION );
		wp_enqueue_style( 'dashicons' );


	}


    /**
     * Print CSS to <head> to avoid an extra request to WordPress through admin-ajax.
     *
     * @since 1.3.1
     */
    public function head_css() {

        if ( ! wp_style_is( 'megamenu', 'enqueued' ) ) {

            echo '<style type="text/css">' . $this->get_inline_css() . "</style>\n";

        }

    }

}

endif;