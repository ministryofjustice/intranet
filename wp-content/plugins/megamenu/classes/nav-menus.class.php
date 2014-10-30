<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists( 'Mega_Menu_Nav_Menus' ) ) :
/**
 * Handles all admin related functionality.
 */
class Mega_Menu_Nav_Menus {


    /**
     * Constructor
     *
     * @since 1.0
     */
    public function __construct() {

        add_action( 'admin_init', array( $this, 'register_nav_meta_box' ), 11 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_menu_page_scripts' ) );
        add_action( 'wp_update_nav_menu_item', array( $this, 'walker_save_fields' ), 10, 3 );
        add_action( 'admin_post_save_megamenu', array( $this, 'save') );
        add_action( 'megamenu_save_settings', array($this, 'save') );

        add_filter( 'hidden_meta_boxes', array( $this, 'show_mega_menu_metabox' ) );
        add_filter( 'wp_edit_nav_menu_walker', array( $this, 'walker' ), 2001 );
        add_filter( 'wp_nav_menu_item_custom_fields', array( $this, 'walker_add_fields' ), 10, 4 );

    }

    /**
     * By default the mega menu meta box is hidden - show it.
     *
     * @since 1.0
     * @param array $hidden
     * @return array
     */
    public function show_mega_menu_metabox( $hidden ) {

        if ( is_array( $hidden ) && count( $hidden ) > 0 ) {
            foreach ( $hidden as $key => $value ) {
                if ( $value == 'mega_menu_meta_box' ) {
                    unset( $hidden[$key] );
                }
            }            
        }

        return $hidden;
    }

    /**
     * Use our custom Nav Edit walker. This adds a filter which we can use to add
     * settings to menu items.
     *
     * @since 1.0
     * @param object $walker
     * @return string
     */
    public function walker( $walker ) {

        require_once MEGAMENU_PATH . 'classes/walker-edit.class.php';

        return 'Mega_Menu_Walker_Edit';

    }


    /**
     * Show custom menu item fields.
     *
     * @since 1.0
     * @param object $item
     * @param int $depth
     * @param array $args
     * @param int $id
     */
    public function walker_add_fields( $id, $item, $depth, $args ) {
        global $wp_registered_sidebars;

        $settings = array_filter( (array) get_post_meta( $item->ID, '_megamenu', true ) );
        
        $defaults = array(
            'type' => 'flyout',
            'align' => 'bottom-left',
            'icon' => 'disabled'
        );

        $settings = array_merge( $defaults, $settings );

        ?>

        <div class="mega-menu-wrap description-wide">
            <h4><?php _e("Mega Menu Options", "megamenu"); ?></h4>
            <p class="description show-on-depth-0-only">
                <a class='button button-secondary megamenu_launch' href='<?php echo admin_url( 'admin-post.php?action=megamenu_editor' ) ?>' data-menu-item-id='<?php echo $item->ID ?>'><?php _e("Configure Panel Widgets for", "megamenu"); echo " " . $item->title ?></a>
            </p>
            <p class="description show-on-depth-0-only">
                <label>
                    <?php _e("Sub Menu Type", "megamenu"); ?>
                    <select name='megamenu[<?php echo $item->ID ?>][type]'>
                        <option value='flyout' <?php selected($settings['type'], 'flyout'); ?>><?php _e("Flyout Menu", "megamenu"); ?></option>
                        <option value='megamenu' <?php selected($settings['type'], 'megamenu'); ?>><?php _e("Mega Menu Panel", "megamenu"); ?></option>
                    </select>
                </label>
            </p>
            <p class="description show-on-depth-0-only">
                <label>
                    <?php _e("Sub Menu Position", "megamenu"); ?>
                    <select name='megamenu[<?php echo $item->ID ?>][align]'>
                        <option value='bottom-left' <?php selected($settings['align'], 'bottom-left'); ?>><?php _e("Left", "megamenu"); ?></option>
                        <option value='bottom-right' <?php selected($settings['align'], 'bottom-right'); ?>><?php _e("Right", "megamenu"); ?></option>
                    </select>
                </label>
            </p>
            <p class="description">
                <label>
                    <?php _e("Menu Icon", "megamenu"); ?>
                    <span class="selected_icon <?php echo $settings['icon'] ?>"></span>
                    <select class='dashicon_dropdown' name='megamenu[<?php echo $item->ID ?>][icon]'>

                        <?php 
                            echo "<option value='disabled'>" . __("Disabled", "megamenu") . "</option>";

                            foreach ($this->all_icons() as $code => $class) {
                                $name = str_replace('dashicons-', '', $class);
                                $name = ucwords(str_replace('-', ' ', $name));
                                echo "<option data-class='{$class}' value='{$class}' " . selected($settings['icon'], $class, false) . ">{$name}</option>";
                            }

                        ?>
                    </select>
                    
                </label>
            </p>
        </div>

        <?php

    }


    /**
     * Save custom menu item fields.
     *
     * @since 1.0
     * @param int $menu_id
     * @param int $menu_item_id
     * @param array $menu_item_args
     */
    public static function walker_save_fields( $menu_id, $menu_item_id, $menu_item_args ) {

        if ( ! empty( $_POST['megamenu'][ $menu_item_id ] ) ) {

            $value = array_filter( (array) $_POST['megamenu'][ $menu_item_id ] );

        } else {

            $value = array();

        }

        if ( ! empty( $value ) ) {

            update_post_meta( $menu_item_id, '_megamenu', $value );

        } else {

            delete_post_meta( $menu_item_id, '_megamenu' );

        }

    }


    /**
     * Adds the meta box container
     *
     * @since 1.0
     */
    public function register_nav_meta_box() {
        global $pagenow;

        if ( 'nav-menus.php' == $pagenow ) {

            add_meta_box(
                'mega_menu_meta_box',
                __("Mega Menu Settings", "megamenu"),
                array( $this, 'metabox_contents' ),
                'nav-menus',
                'side',
                'high'
            );

        }

    }


    /**
     * Enqueue required CSS and JS for Mega Menu
     *
     * @since 1.0
     */
    public function enqueue_menu_page_scripts($hook) {

        if( 'nav-menus.php' != $hook )
            return;
        
        // http://wordpress.org/plugins/image-widget/
        if ( class_exists( 'Tribe_Image_Widget' ) ) {
            $image_widget = new Tribe_Image_Widget;
            $image_widget->admin_setup();
        }

        wp_enqueue_style( 'colorbox', MEGAMENU_BASE_URL . 'js/colorbox/colorbox.css', false, MEGAMENU_VERSION );
        wp_enqueue_style( 'mega-menu', MEGAMENU_BASE_URL . 'css/admin.css', false, MEGAMENU_VERSION );
        wp_enqueue_style( 'mega-menu-editor', MEGAMENU_BASE_URL . 'css/editor.css', false, MEGAMENU_VERSION );
        wp_enqueue_style( 'font-awesome', MEGAMENU_BASE_URL . 'css/font-awesome.min.css', false, MEGAMENU_VERSION );

        wp_enqueue_script( 'mega-menu', MEGAMENU_BASE_URL . 'js/admin.js', array(
            'jquery',
            'jquery-ui-core',
            'jquery-ui-sortable',
            'jquery-ui-accordion'),
        MEGAMENU_VERSION );

        wp_enqueue_script( 'colorbox', MEGAMENU_BASE_URL . 'js/colorbox/jquery.colorbox-min.js', array( 'jquery' ), MEGAMENU_VERSION );
        wp_enqueue_script( 'ddslick', MEGAMENU_BASE_URL . 'js/ddslick/jquery.ddslick.js', array( 'jquery' ), MEGAMENU_VERSION );

        wp_localize_script( 'mega-menu', 'megamenu',
            array(
                'debug_launched' => __("Launched for Menu ID", "megamenu"),
                'debug_added' => __("Added to list", "megamenu"),
                'select_a_widget' => __("Select a widget to display in the Mega Panel", "megamenu"),
                'nonce' => wp_create_nonce('megamenu_edit_widgets'),
                'nonce_check_failed' => __("Oops. Something went wrong. Please reload the page.", "megamenu")
            )
        );

        do_action("megamenu_enqueue_admin_scripts");

    }

    /**
     * Show the Meta Menu settings
     *
     * @since 1.0
     */
    public function metabox_contents() {

        $menu_id = $this->get_selected_menu_id();

        do_action("megamenu_save_settings");

        $this->print_enable_megamenu_options( $menu_id );

    }


    /**
     * Save the mega menu settings (submitted from Menus Page Meta Box)
     *
     * @since 1.0
     */
    public function save() {

        if ( isset( $_POST['menu'] ) && $_POST['menu'] > 0 && is_nav_menu( $_POST['menu'] ) && isset( $_POST['megamenu_meta'] ) ) {

            $submitted_settings = $_POST['megamenu_meta'];

            if ( ! get_site_option( 'megamenu_settings' ) ) {

                add_site_option( 'megamenu_settings', $submitted_settings );

            } else {

                $existing_settings = get_site_option( 'megamenu_settings' );

                $new_settings = array_merge( $existing_settings, $submitted_settings );

                update_site_option( 'megamenu_settings', $new_settings );

            }

            do_action( "megamenu_after_save_settings" );

        }

    }


    /**
     * Print the custom Meta Box settings
     *
     * @param int $menu_id
     */
    public function print_enable_megamenu_options( $menu_id ) {

        $tagged_menu_locations = $this->get_tagged_theme_locations_for_menu_id( $menu_id );
        $theme_locations = get_registered_nav_menus();

        $saved_settings = get_site_option( 'megamenu_settings' );

        if ( ! count( $theme_locations ) ) {

            echo "<p>" . __("This theme does not have any menu locations.", "megamenu") . "</p>";

        } else if ( ! count ( $tagged_menu_locations ) ) {

            echo "<p>" . __("This menu is not tagged to a location. Please tag a location to enable the Mega Menu settings.", "megamenu") . "</p>";

        } else { ?>

            <?php if ( count( $tagged_menu_locations ) == 1 ) : ?>
            
                <?php 

                $locations = array_keys( $tagged_menu_locations );
                $location = $locations[0];

                if (isset( $tagged_menu_locations[ $location ] ) ) {
                    $this->settings_table( $location, $saved_settings ); 
                }
                
                ?>

            <?php else: ?>

                <div id='megamenu_accordion'>

                    <?php foreach ( $theme_locations as $location => $name ) : ?>
                    
                        <?php if ( isset( $tagged_menu_locations[ $location ] ) ): ?>

                            <h3 class='theme_settings'><?php echo $name; ?></h3>

                            <div class='accordion_content' style='display: none;'>
                                <?php $this->settings_table( $location, $saved_settings ); ?>
                            </div>
                            
                        <?php endif; ?>
                    
                    <?php endforeach;?>
                </div>

            <?php endif; ?>

            <?php 

            submit_button( __( 'Save' ), 'button-primary alignright');

        }

    }

    /**
     * Print the list of Mega Menu settings
     *
     * @since 1.0
     */
    public function settings_table( $location, $settings ) {
        ?>
        <table>
            <tr>
                <td><?php _e("Enable", "megamenu") ?></td>
                <td>
                    <input type='checkbox' name='megamenu_meta[<?php echo $location ?>][enabled]' value='1' <?php checked( isset( $settings[$location]['enabled'] ) ); ?> />
                </td>
            </tr>
            <tr>
                <td><?php _e("Event", "megamenu") ?></td>
                <td>
                    <select name='megamenu_meta[<?php echo $location ?>][event]'>
                        <option value='hover' <?php selected( isset( $settings[$location]['event'] ) && $settings[$location]['event'] == 'hover'); ?>><?php _e("Hover", "megamenu"); ?></option>
                        <option value='click' <?php selected( isset( $settings[$location]['event'] ) && $settings[$location]['event'] == 'click'); ?>><?php _e("Click", "megamenu"); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php _e("Effect", "megamenu") ?></td>
                <td>
                    <select name='megamenu_meta[<?php echo $location ?>][effect]'>
                        <option value='disabled' <?php selected( isset( $settings[$location]['effect'] ) && $settings[$location]['effect'] == 'disabled'); ?>><?php _e("None", "megamenu"); ?></option>
                        <option value='fade' <?php selected( isset( $settings[$location]['effect'] ) && $settings[$location]['effect'] == 'fade'); ?>><?php _e("Fade", "megamenu"); ?></option>
                        <option value='slide' <?php selected( isset( $settings[$location]['effect'] ) && $settings[$location]['effect'] == 'slide'); ?>><?php _e("Slide", "megamenu"); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php _e("Theme", "megamenu"); ?></td>
                <td>

                    <select name='megamenu_meta[<?php echo $location ?>][theme]'>
                        <?php 
                            $style_manager = new Mega_Menu_Style_Manager();
                            $themes = $style_manager->get_themes();

                            foreach ( $themes as $key => $theme ) {
                                echo "<option value='{$key}' " . selected( $settings[$location]['theme'], $key ) . ">{$theme['title']}</option>";
                            }
                        ?>
                    </select>
                </td>
            </tr>

            <?php do_action('megamenu_settings_table', $location, $settings); ?>
        </table>
        <?php
    }


    /**
     * Return the locations that a specific menu ID has been tagged to.
     *
     * @param $menu_id int
     * @return array
     */
    public function get_tagged_theme_locations_for_menu_id( $menu_id ) {

        $locations = array();

        $nav_menu_locations = get_nav_menu_locations();

        foreach ( get_registered_nav_menus() as $id => $name ) {

            if ( isset( $nav_menu_locations[ $id ] ) && $nav_menu_locations[$id] == $menu_id )
                $locations[$id] = $name;

        }

        return $locations;
    }

    /**
     * Get the current menu ID.
     *
     * Most of this taken from wp-admin/nav-menus.php (no built in functions to do this)
     *
     * @since 1.0
     * @return int
     */
    public function get_selected_menu_id() {

        $nav_menus = wp_get_nav_menus( array('orderby' => 'name') );

        $menu_count = count( $nav_menus );

        $nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int) $_REQUEST['menu'] : 0;

        $add_new_screen = ( isset( $_GET['menu'] ) && 0 == $_GET['menu'] ) ? true : false;

        // If we have one theme location, and zero menus, we take them right into editing their first menu
        $page_count = wp_count_posts( 'page' );
        $one_theme_location_no_menus = ( 1 == count( get_registered_nav_menus() ) && ! $add_new_screen && empty( $nav_menus ) && ! empty( $page_count->publish ) ) ? true : false;

        // Get recently edited nav menu
        $recently_edited = absint( get_user_option( 'nav_menu_recently_edited' ) );
        if ( empty( $recently_edited ) && is_nav_menu( $nav_menu_selected_id ) )
            $recently_edited = $nav_menu_selected_id;

        // Use $recently_edited if none are selected
        if ( empty( $nav_menu_selected_id ) && ! isset( $_GET['menu'] ) && is_nav_menu( $recently_edited ) )
            $nav_menu_selected_id = $recently_edited;

        // On deletion of menu, if another menu exists, show it
        if ( ! $add_new_screen && 0 < $menu_count && isset( $_GET['action'] ) && 'delete' == $_GET['action'] )
            $nav_menu_selected_id = $nav_menus[0]->term_id;

        // Set $nav_menu_selected_id to 0 if no menus
        if ( $one_theme_location_no_menus ) {
            $nav_menu_selected_id = 0;
        } elseif ( empty( $nav_menu_selected_id ) && ! empty( $nav_menus ) && ! $add_new_screen ) {
            // if we have no selection yet, and we have menus, set to the first one in the list
            $nav_menu_selected_id = $nav_menus[0]->term_id;
        }

        return $nav_menu_selected_id;

    }

    /**
     * List of all available DashIcon classes.
     *
     * @since 1.0
     * @return array - Sorted list of icon classes
     */
    private function all_icons() {

        $icons = array(
            'dash-f333' => 'dashicons-menu',
            'dash-f319' => 'dashicons-admin-site',
            'dash-f226' => 'dashicons-dashboard',
            'dash-f109' => 'dashicons-admin-post',
            'dash-f104' => 'dashicons-admin-media',
            'dash-f103' => 'dashicons-admin-links',
            'dash-f105' => 'dashicons-admin-page',
            'dash-f101' => 'dashicons-admin-comments',
            'dash-f100' => 'dashicons-admin-appearance',
            'dash-f106' => 'dashicons-admin-plugins',
            'dash-f110' => 'dashicons-admin-users',
            'dash-f107' => 'dashicons-admin-tools',
            'dash-f108' => 'dashicons-admin-settings',
            'dash-f112' => 'dashicons-admin-network',
            'dash-f102' => 'dashicons-admin-home',
            'dash-f111' => 'dashicons-admin-generic',
            'dash-f148' => 'dashicons-admin-collapse',
            'dash-f119' => 'dashicons-welcome-write-blog',
            'dash-f133' => 'dashicons-welcome-add-page',
            'dash-f115' => 'dashicons-welcome-view-site',
            'dash-f116' => 'dashicons-welcome-widgets-menus',
            'dash-f117' => 'dashicons-welcome-comments',
            'dash-f118' => 'dashicons-welcome-learn-more',
            'dash-f123' => 'dashicons-format-aside',
            'dash-f128' => 'dashicons-format-image',
            'dash-f161' => 'dashicons-format-gallery',
            'dash-f126' => 'dashicons-format-video',
            'dash-f130' => 'dashicons-format-status',
            'dash-f122' => 'dashicons-format-quote',
            'dash-f125' => 'dashicons-format-chat',
            'dash-f127' => 'dashicons-format-audio',
            'dash-f306' => 'dashicons-camera',
            'dash-f232' => 'dashicons-images-alt',
            'dash-f233' => 'dashicons-images-alt2',
            'dash-f234' => 'dashicons-video-alt',
            'dash-f235' => 'dashicons-video-alt2',
            'dash-f236' => 'dashicons-video-alt3',
            'dash-f501' => 'dashicons-media-archive',
            'dash-f500' => 'dashicons-media-audio',
            'dash-f499' => 'dashicons-media-code',
            'dash-f498' => 'dashicons-media-default',
            'dash-f497' => 'dashicons-media-document',
            'dash-f496' => 'dashicons-media-interactive',
            'dash-f495' => 'dashicons-media-spreadsheet',
            'dash-f491' => 'dashicons-media-text',
            'dash-f490' => 'dashicons-media-video',
            'dash-f492' => 'dashicons-playlist-audio',
            'dash-f493' => 'dashicons-playlist-video',
            'dash-f165' => 'dashicons-image-crop',
            'dash-f166' => 'dashicons-image-rotate-left',
            'dash-f167' => 'dashicons-image-rotate-right',
            'dash-f168' => 'dashicons-image-flip-vertical',
            'dash-f169' => 'dashicons-image-flip-horizontal',
            'dash-f171' => 'dashicons-undo',
            'dash-f172' => 'dashicons-redo',
            'dash-f200' => 'dashicons-editor-bold',
            'dash-f201' => 'dashicons-editor-italic',
            'dash-f203' => 'dashicons-editor-ul',
            'dash-f204' => 'dashicons-editor-ol',
            'dash-f205' => 'dashicons-editor-quote',
            'dash-f206' => 'dashicons-editor-alignleft',
            'dash-f207' => 'dashicons-editor-aligncenter',
            'dash-f208' => 'dashicons-editor-alignright',
            'dash-f209' => 'dashicons-editor-insertmore',
            'dash-f210' => 'dashicons-editor-spellcheck',
            'dash-f211' => 'dashicons-editor-expand',
            'dash-f506' => 'dashicons-editor-contract',
            'dash-f212' => 'dashicons-editor-kitchensink',
            'dash-f213' => 'dashicons-editor-underline',
            'dash-f214' => 'dashicons-editor-justify',
            'dash-f215' => 'dashicons-editor-textcolor',
            'dash-f216' => 'dashicons-editor-paste-word',
            'dash-f217' => 'dashicons-editor-paste-text',
            'dash-f218' => 'dashicons-editor-removeformatting',
            'dash-f219' => 'dashicons-editor-video',
            'dash-f220' => 'dashicons-editor-customchar',
            'dash-f221' => 'dashicons-editor-outdent',
            'dash-f222' => 'dashicons-editor-indent',
            'dash-f223' => 'dashicons-editor-help',
            'dash-f224' => 'dashicons-editor-strikethrough',
            'dash-f225' => 'dashicons-editor-unlink',
            'dash-f320' => 'dashicons-editor-rtl',
            'dash-f464' => 'dashicons-editor-break',
            'dash-f475' => 'dashicons-editor-code',
            'dash-f476' => 'dashicons-editor-paragraph',
            'dash-f135' => 'dashicons-align-left',
            'dash-f136' => 'dashicons-align-right',
            'dash-f134' => 'dashicons-align-center',
            'dash-f138' => 'dashicons-align-none',
            'dash-f160' => 'dashicons-lock',
            'dash-f145' => 'dashicons-calendar',
            'dash-f177' => 'dashicons-visibility',
            'dash-f173' => 'dashicons-post-status',
            'dash-f464' => 'dashicons-edit',
            'dash-f182' => 'dashicons-trash',
            'dash-f504' => 'dashicons-external',
            'dash-f142' => 'dashicons-arrow-up',
            'dash-f140' => 'dashicons-arrow-down',
            'dash-f139' => 'dashicons-arrow-right',
            'dash-f141' => 'dashicons-arrow-left',
            'dash-f342' => 'dashicons-arrow-up-alt',
            'dash-f346' => 'dashicons-arrow-down-alt',
            'dash-f344' => 'dashicons-arrow-right-alt',
            'dash-f340' => 'dashicons-arrow-left-alt',
            'dash-f343' => 'dashicons-arrow-up-alt2',
            'dash-f347' => 'dashicons-arrow-down-alt2',
            'dash-f345' => 'dashicons-arrow-right-alt2',
            'dash-f341' => 'dashicons-arrow-left-alt2',
            'dash-f156' => 'dashicons-sort',
            'dash-f229' => 'dashicons-leftright',
            'dash-f503' => 'dashicons-randomize',
            'dash-f163' => 'dashicons-list-view',
            'dash-f164' => 'dashicons-exerpt-view',
            'dash-f237' => 'dashicons-share',
            'dash-f240' => 'dashicons-share-alt',
            'dash-f242' => 'dashicons-share-alt2',
            'dash-f301' => 'dashicons-twitter',
            'dash-f303' => 'dashicons-rss',
            'dash-f465' => 'dashicons-email',
            'dash-f466' => 'dashicons-email-alt',
            'dash-f304' => 'dashicons-facebook',
            'dash-f305' => 'dashicons-facebook-alt',
            'dash-f462' => 'dashicons-googleplus',
            'dash-f325' => 'dashicons-networking',
            'dash-f308' => 'dashicons-hammer',
            'dash-f309' => 'dashicons-art',
            'dash-f310' => 'dashicons-migrate',
            'dash-f311' => 'dashicons-performance',
            'dash-f483' => 'dashicons-universal-access',
            'dash-f507' => 'dashicons-universal-access-alt',
            'dash-f486' => 'dashicons-tickets',
            'dash-f484' => 'dashicons-nametag',
            'dash-f481' => 'dashicons-clipboard',
            'dash-f487' => 'dashicons-heart',
            'dash-f488' => 'dashicons-megaphone',
            'dash-f489' => 'dashicons-schedule',
            'dash-f120' => 'dashicons-wordpress',
            'dash-f324' => 'dashicons-wordpress-alt',
            'dash-f157' => 'dashicons-pressthis',
            'dash-f463' => 'dashicons-update',
            'dash-f180' => 'dashicons-screenoptions',
            'dash-f348' => 'dashicons-info',
            'dash-f174' => 'dashicons-cart',
            'dash-f175' => 'dashicons-feedback',
            'dash-f176' => 'dashicons-cloud',
            'dash-f326' => 'dashicons-translation',
            'dash-f323' => 'dashicons-tag',
            'dash-f318' => 'dashicons-category',
            'dash-f478' => 'dashicons-archive',
            'dash-f479' => 'dashicons-tagcloud',
            'dash-f480' => 'dashicons-text',
            'dash-f147' => 'dashicons-yes',
            'dash-f158' => 'dashicons-no',
            'dash-f335' => 'dashicons-no-alt',
            'dash-f132' => 'dashicons-plus',
            'dash-f502' => 'dashicons-plus-alt',
            'dash-f460' => 'dashicons-minus',
            'dash-f153' => 'dashicons-dismiss',
            'dash-f159' => 'dashicons-marker',
            'dash-f155' => 'dashicons-star-filled',
            'dash-f459' => 'dashicons-star-half',
            'dash-f154' => 'dashicons-star-empty',
            'dash-f227' => 'dashicons-flag',
            'dash-f230' => 'dashicons-location',
            'dash-f231' => 'dashicons-location-alt',
            'dash-f178' => 'dashicons-vault',
            'dash-f332' => 'dashicons-shield',
            'dash-f334' => 'dashicons-shield-alt',
            'dash-f468' => 'dashicons-sos',
            'dash-f179' => 'dashicons-search',
            'dash-f181' => 'dashicons-slides',
            'dash-f183' => 'dashicons-analytics',
            'dash-f184' => 'dashicons-chart-pie',
            'dash-f185' => 'dashicons-chart-bar',
            'dash-f238' => 'dashicons-chart-line',
            'dash-f239' => 'dashicons-chart-area',
            'dash-f307' => 'dashicons-groups',
            'dash-f338' => 'dashicons-businessman',
            'dash-f336' => 'dashicons-id',
            'dash-f337' => 'dashicons-id-alt',
            'dash-f312' => 'dashicons-products',
            'dash-f313' => 'dashicons-awards',
            'dash-f314' => 'dashicons-forms',
            'dash-f473' => 'dashicons-testimonial',
            'dash-f322' => 'dashicons-portfolio',
            'dash-f330' => 'dashicons-book',
            'dash-f331' => 'dashicons-book-alt',
            'dash-f316' => 'dashicons-download',
            'dash-f317' => 'dashicons-upload',
            'dash-f321' => 'dashicons-backup',
            'dash-f469' => 'dashicons-clock',
            'dash-f339' => 'dashicons-lightbulb',
            'dash-f482' => 'dashicons-microphone',
            'dash-f472' => 'dashicons-desktop',
            'dash-f471' => 'dashicons-tablet',
            'dash-f470' => 'dashicons-smartphone',
            'dash-f328' => 'dashicons-smiley'
        );

        $icons = apply_filters( "megamenu_icons", $icons );

        sort( $icons );

        return $icons;
    }

}

endif;