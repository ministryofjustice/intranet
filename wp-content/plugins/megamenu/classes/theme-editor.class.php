<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists( 'Mega_Menu_theme_Editor' ) ) :

/**
 * Handles all admin related functionality.
 */
class Mega_Menu_theme_Editor {


    /**
     * All themes (default and custom)
     */
    var $themes = array();


    /**
     * Active theme
     */
    var $active_theme = array();


    /**
     * Active theme ID
     */
    var $id = "";


    /**
     * Constructor
     *
     * @since 1.0
     */
    public function __construct() {

        add_action( 'admin_post_megamenu_save_theme', array( $this, 'save') );
        add_action( 'admin_post_megamenu_add_theme', array( $this, 'create') );
        add_action( 'admin_post_megamenu_delete_theme', array( $this, 'delete') );
        add_action( 'admin_post_megamenu_revert_theme', array( $this, 'revert') );
        add_action( 'admin_post_megamenu_duplicate_theme', array( $this, 'duplicate') );

        add_action( 'admin_menu', array( $this, 'megamenu_themes_page') );
        add_action( "admin_enqueue_scripts", array( $this, 'enqueue_theme_editor_scripts' ) );

        if ( class_exists( "Mega_Menu_Style_Manager" ) ) {

            $style_manager = new Mega_Menu_Style_Manager();

            $this->themes = $style_manager->get_themes();
            $this->id = isset( $_GET['theme'] ) ? $_GET['theme'] : 'default';
            $this->active_theme = $this->themes[$this->id];

        }
    }

    /**
     * Save changes to an exiting theme.
     *
     * @since 1.0
     */
    public function save() {

        check_admin_referer( 'megamenu_save_theme' );

        $theme = esc_attr( $_POST['theme_id'] );

        $saved_themes = get_site_option( "megamenu_themes" );

        if ( isset( $saved_themes[ $theme ] ) ) {
            unset( $saved_themes[ $theme ] );
        }

        $saved_themes[ $theme ] = array_map( 'esc_attr', $_POST['settings'] );

        update_site_option( "megamenu_themes", $saved_themes );

        do_action("megamenu_after_theme_save");

        wp_redirect( admin_url( "themes.php?page=megamenu_theme_editor&theme={$theme}&saved=true" ) );

    }


    /**
     * Duplicate an existing theme.
     *
     * @since 1.0
     */
    public function duplicate() {

        check_admin_referer( 'megamenu_duplicate_theme' );

        $theme = esc_attr( $_GET['theme_id'] );

        $copy = $this->themes[$theme];

        $saved_themes = get_site_option( "megamenu_themes" );

        $next_id = $this->get_next_theme_id();

        $copy['title'] = $copy['title'] . " " . __('Copy', 'megamenu');

        $new_theme_id = "custom_theme_" . $next_id;

        $saved_themes[ 'custom_theme_' . $next_id ] = $copy;

        update_site_option( "megamenu_themes", $saved_themes );

        do_action("megamenu_after_theme_duplicate");

        wp_redirect( admin_url( "themes.php?page=megamenu_theme_editor&theme={$new_theme_id}&duplicated=true") );

    }


    /**
     * Delete a theme
     * 
     * @since 1.0
     */
    public function delete() {

        check_admin_referer( 'megamenu_delete_theme' );

        $theme = esc_attr( $_GET['theme_id'] );

        if ( $this->theme_is_being_used_by_menu( $theme ) ) {

            wp_redirect( admin_url( "themes.php?page=megamenu_theme_editor&theme={$theme}&deleted=false") );
            return;
        }

        $saved_themes = get_site_option( "megamenu_themes" );

        if ( isset( $saved_themes[$theme] ) ) {
            unset( $saved_themes[$theme] );
        }

        update_site_option( "megamenu_themes", $saved_themes );

        do_action("megamenu_after_theme_delete");

        wp_redirect( admin_url( "themes.php?page=megamenu_theme_editor&theme=default&deleted=true") );

    }


    /**
     * Revert a theme (only available for default themes, you can't revert a custom theme)
     *
     * @since 1.0
     */
    public function revert() {

        check_admin_referer( 'megamenu_revert_theme' );

        $theme = esc_attr( $_GET['theme_id'] );

        $saved_themes = get_site_option( "megamenu_themes" );

        if ( isset( $saved_themes[$theme] ) ) {
            unset( $saved_themes[$theme] );
        }

        update_site_option( "megamenu_themes", $saved_themes );

        do_action("megamenu_after_theme_revert");

        wp_redirect( admin_url( "themes.php?page=megamenu_theme_editor&theme={$theme}&reverted=true") );

    }


    /**
     * Create a new custom theme
     *
     * @since 1.0
     */
    public function create() {

        check_admin_referer( 'megamenu_create_theme' );

        $saved_themes = get_site_option( "megamenu_themes" );

        $next_id = $this->get_next_theme_id();

        $new_theme_id = "custom_theme_" . $next_id;

        $new_theme = $this->themes['default'];

        $new_theme['title'] = "Custom {$next_id}";

        $saved_themes[$new_theme_id] = $new_theme;

        update_site_option( "megamenu_themes", $saved_themes );

        do_action("megamenu_after_theme_create");

        wp_redirect( admin_url( "themes.php?page=megamenu_theme_editor&theme={$new_theme_id}&created=true") );

    }


    /**
     * Returns the next available custom theme ID
     *
     * @since 1.0
     */
    public function get_next_theme_id() {
        
        $last_id = 0;

        if ( $saved_themes = get_site_option( "megamenu_themes" ) ) {

            foreach ( $saved_themes as $key => $value ) {

                if ( strpos( $key, 'custom_theme' ) !== FALSE ) {

                    $parts = explode( "_", $key );
                    $theme_id = end( $parts );

                    if ($theme_id > $last_id) {
                        $last_id = $theme_id;
                    }       

                }

            }

        }

        $next_id = $last_id + 1;

        return $next_id;
    }


    /**
     * Checks to see if a certain theme is in use.
     *
     * @since 1.0
     * @param string $theme
     */
    public function theme_is_being_used_by_menu( $theme ) {
        $settings = get_site_option( "megamenu_settings" );

        if ( ! $settings ) {
            return false;
        }

        $locations = get_nav_menu_locations();

        if ( count( $locations ) ) {

            foreach ( $locations as $location => $menu_id ) {

                if ( has_nav_menu( $location ) && isset( $settings[ $location ]['theme'] ) && $settings[ $location ]['theme'] == $theme ) {
                    return true;
                }

            }

        }

        return false;
    }


    /**
     * Adds the "Menu Themes" menu item and page.
     *
     * @since 1.0
     */
    public function megamenu_themes_page() {

        $page = add_theme_page(__('Mega Menu Themes', 'megamenu'), __('Menu Themes', 'megamenu'), 'edit_theme_options', 'megamenu_theme_editor', array($this, 'theme_editor' ) );
    
    }


    /**
     * Main Menu Themes page content
     *
     * @since 1.0
     */
    public function theme_editor() {

        ?>

        <div class='megamenu_wrap'>
            <div class='megamenu_right'>
                <div class='theme_settings'>
                    <?php $this->print_messages(); ?>
                    <?php echo $this->form(); ?>
                </div>
            </div>
        </div>

        <div class='megamenu_left'>
            <h4><?php _e("Select theme to edit", "megamenu"); ?></h4>
            <ul class='megamenu_theme_selector'>
                <?php echo $this->theme_selector(); ?>
            </ul>
            <a href='<?php echo wp_nonce_url(admin_url("admin-post.php?action=megamenu_add_theme"), 'megamenu_create_theme') ?>'><?php _e("Create a new theme", "megamenu"); ?></a>
        </div>

        <?php
    }


    /**
     * Display messages to the user
     *
     * @since 1.0
     */
    public function print_messages() {

        $style_manager = new Mega_Menu_Style_Manager();

        $test = $style_manager->generate_css_for_location( 'test', $this->active_theme, 0 );

        if ( is_wp_error( $test ) ) {
            echo "<p class='fail'>" . $test->get_error_message() . "</p>";
        }

        if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == 'false' ) {
            echo "<p class='fail'>" . __("Failed to delete theme. The theme is in use by a menu.", "megamenu") . "</p>";
        }

        if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == 'true' ) {
            echo "<p class='success'>" . __("Theme Deleted", "megamenu") . "</p>";
        }

        if ( isset( $_GET['duplicated'] ) ) {
            echo "<p class='success'>" . __("Theme Duplicated", "megamenu") . "</p>";
        }

        if ( isset( $_GET['saved'] ) ) {
            echo "<p class='success'>" . __("Changes Saved", "megamenu") . "</p>";
        }

        if ( isset( $_GET['reverted'] ) ) {
            echo "<p class='success'>" . __("Theme Reverted", "megamenu") . "</p>";
        }

        if ( isset( $_GET['created'] ) ) {
            echo "<p class='success'>" . __("New Theme Created", "megamenu") . "</p>";
        }

    }


    /**
     * Lists the available themes
     *
     * @since 1.0
     */
    public function theme_selector() {

        $list_items = "";

        foreach ( $this->themes as $id => $theme ) {
            $class = $id == $this->id ? 'mega_active' : '';

            $style_manager = new Mega_Menu_Style_Manager();
            $test = $style_manager->generate_css_for_location( 'tmp-location', $theme, 0 );
            $error = is_wp_error( $test ) ? 'error' : '';

            $list_items .= "<li class='{$class} {$error}'><a href='" . admin_url("themes.php?page=megamenu_theme_editor&theme={$id}") . "'>{$theme['title']}</a></li>";
        }

        return $list_items;

    }


    /**
     * Checks to see if a given string contains any of the provided search terms
     *
     * @param srgin $key
     * @param array $needles
     * @since 1.0
     */
    private function string_contains( $key, $needles ) {

        foreach ( $needles as $needle ) {

            if ( strpos( $key, $needle ) !== FALSE ) { 
                return true;
            }
        }

        return false;

    }


    /**
     * Displays the theme editor form.
     *
     * @since 1.0
     */
    public function form() {
        
        ?>

        <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
            <input type="hidden" name="theme_id" value="<?php echo $this->id; ?>" />
            <input type="hidden" name="action" value="megamenu_save_theme" />
            <?php wp_nonce_field( 'megamenu_save_theme' ); ?>
            
            <h4><?php _e("General Settings", "megamenu"); ?></h4>

            <table>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Theme Title", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'><?php $this->print_theme_freetext_option( 'title' ); ?></td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Arrow Up", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Select the 'Up' arrow style.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'><?php $this->print_theme_arrow_option( 'arrow_up' ); ?></td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Arrow Down", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Select the 'Down' arrow style.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'><?php $this->print_theme_arrow_option( 'arrow_down' ); ?></td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Arrow Left", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Select the 'Left' arrow style.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'><?php $this->print_theme_arrow_option( 'arrow_left' ); ?></td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Arrow Right", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Select the 'Right' arrow style.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'><?php $this->print_theme_arrow_option( 'arrow_right' ); ?></td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Main Font", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the main font to use for panel contents and flyout menu items.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_color_option( 'font_color' ); ?>
                        <?php $this->print_theme_freetext_option( 'font_size' ); ?>
                        <?php $this->print_theme_font_option( 'font_family' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Responsive Breakpoint", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the width at which the menu turns into a mobile menu.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'><?php $this->print_theme_freetext_option( 'responsive_breakpoint' ); ?></td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Line Height", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the general line height to use in the panel contents.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'><?php $this->print_theme_freetext_option( 'line_height' ); ?></td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Z-Index", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the z-index to ensure the panels appear ontop of other content.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'><?php $this->print_theme_freetext_option( 'z_index' ); ?></td>
                </tr>
            </table>

            <h4><?php _e("Menu Bar", "megamenu"); ?></h4>

            <table>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Menu Background", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("The background color for the main menu bar. Set each value to transparent for a 'button' style menu.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("From", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'container_background_from' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("To", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'container_background_to' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Menu Padding", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Padding for the main menu bar.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'container_padding_top' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'container_padding_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'container_padding_bottom' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'container_padding_left' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Rounded Corners", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set a border radius on the main menu bar.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'container_border_radius_top_left' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'container_border_radius_top_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'container_border_radius_bottom_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'container_border_radius_bottom_left' ); ?>
                        </label>
                    </td>
                </tr>
            </table>

            <h4><?php _e("Top Level Menu Items", "megamenu"); ?></h4>

            <table>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Menu Item Background", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("The background color for each top level menu item. Tip: Set these values to transparent if you've already set a background color on the menu container.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("From", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'menu_item_background_from' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("To", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'menu_item_background_to' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Menu Item Background (Hover)", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("The background color for a top level menu item (on hover).", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("From", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'menu_item_background_hover_from' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("To", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'menu_item_background_hover_to' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Menu Item Spacing", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Define the size of the gap between each top level menu item.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_freetext_option( 'menu_item_spacing' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Menu Item Height", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Define the height of each top level menu item. This value, plus the container top and bottom padding values define the overall height of the menu bar.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_freetext_option( 'menu_item_link_height' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Font", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("The font to use for each top level menu item.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_color_option( 'menu_item_link_color' ); ?>
                        <?php $this->print_theme_freetext_option( 'menu_item_link_font_size' ); ?>
                        <?php $this->print_theme_font_option( 'menu_item_link_font_family' ); ?>
                        <?php $this->print_theme_weight_option( 'menu_item_link_font_weight' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Font (Hover)", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the font to use for each top level menu item (on hover).", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_color_option( 'menu_item_link_color_hover' ); ?>
                        <?php $this->print_theme_weight_option( 'menu_item_link_font_weight_hover' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Text Transform", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the padding for the headings. Use this to set the gap between the widget heading and the widget content.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_transform_option( 'menu_item_link_text_transform' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Menu Item Padding", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the padding for each top level menu item.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'menu_item_link_padding_top' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'menu_item_link_padding_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'menu_item_link_padding_bottom' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'menu_item_link_padding_left' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Menu Item Rounded Corners", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set rounded corners for each top level menu item.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'menu_item_link_border_radius_top_left' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'menu_item_link_border_radius_top_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'menu_item_link_border_radius_bottom_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'menu_item_link_border_radius_bottom_left' ); ?>
                        </label>
                    </td>
                </tr>
            </table>

            <h4><?php _e("Mega Panels", "megamenu"); ?></h4>

            <table>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Panel Background", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set a background color for a whole panel.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("From", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'panel_background_from' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("To", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'panel_background_to' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Panel Width", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Mega Panel width. Note: A 100% wide panel will only ever be as wide as the menu itself. For a fixed panel width set this to a pixel value.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_freetext_option( 'panel_width' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Panel Border", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the border to display on the Mega Panel.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("Color", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'panel_border_color' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_border_top' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_border_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_border_bottom' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_border_left' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Panel Padding", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the padding for the whole panel. Set these values 0px if you wish your panel content to go edge-to-edge.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_padding_top' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_padding_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_padding_bottom' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_padding_left' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Rounded Corners", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set rounded corners for the panel.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_border_radius_top_left' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_border_radius_top_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_border_radius_bottom_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_border_radius_bottom_left' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Widget Padding", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the padding for each widget in the panel. Use this to define the spacing between each widget in the panel.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_widget_padding_top' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_widget_padding_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_widget_padding_bottom' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_widget_padding_left' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Heading Font", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the font to use for Widget Headers. This setting is also used for second level menu items when they're displayed in a Mega Menu.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_color_option( 'panel_header_color' ); ?>
                        <?php $this->print_theme_freetext_option( 'panel_header_font_size' ); ?>
                        <?php $this->print_theme_font_option( 'panel_header_font_family' ); ?>
                        <?php $this->print_theme_weight_option( 'panel_header_font_weight' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Heading Text Transform", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the text transform style for the Widget Headers and second level menu items.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_transform_option( 'panel_header_text_transform' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Heading Padding", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the padding for the headings. Use this to set the gap between the widget heading and the widget content.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_header_padding_top' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_header_padding_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_header_padding_bottom' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'panel_header_padding_left' ); ?>
                        </label>
                    </td>
                </tr>
            </table>


            <h4><?php _e("Flyout Menus", "megamenu"); ?></h4>

            <table>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Item Background", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the background color for a flyout menu item.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("From", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'flyout_background_from' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("To", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'flyout_background_to' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Item Background (Hover)", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the background color for a flyout menu item (on hover).", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("From", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'flyout_background_hover_from' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("To", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'flyout_background_hover_to' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Item Height", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("The height of each flyout menu item.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_freetext_option( 'flyout_link_height' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Item Padding", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the padding for each flyout menu item.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'flyout_link_padding_top' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'flyout_link_padding_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'flyout_link_padding_bottom' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'flyout_link_padding_left' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Flyout Menu Width", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("The width of each flyout menu.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_freetext_option( 'flyout_width' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Flyout Menu Border", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the border for the flyout menu.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <label>
                            <span class='mega-short-desc'><?php _e("Color", "megamenu"); ?></span>
                            <?php $this->print_theme_color_option( 'flyout_border_color' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Top", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'flyout_border_top' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Right", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'flyout_border_right' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Bottom", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'flyout_border_bottom' ); ?>
                        </label>
                        <label>
                            <span class='mega-short-desc'><?php _e("Left", "megamenu"); ?></span>
                            <?php $this->print_theme_freetext_option( 'flyout_border_left' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Font Weight", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the font weight for the flyout menu items.", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_weight_option( 'flyout_link_weight' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Font Weight (Hover)", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Set the font weight for the flyout menu items (on hover).", "megamenu"); ?>
                        </div>
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_weight_option( 'flyout_link_weight_hover' ); ?>
                    </td>
                </tr>
            </table>

            <h4><?php _e("Custom Styling", "megamenu"); ?></h4>

            <table>
                <tr>
                    <td class='mega-name'>
                        <?php _e("CSS Editor", "megamenu"); ?>
                        <div class='mega-description'>
                            <?php _e("Define any custom CSS you wish to add to menus using this theme. You can use standard CSS or SCSS.", "megamenu"); ?>
                        </div>
                        
                    </td>
                    <td class='mega-value'>
                        <?php $this->print_theme_textarea_option( 'custom_css' ); ?>
                        <p><b><?php _e("Custom Styling Tips", "megamenu"); ?></b></p>
                        <ul class='custom_styling_tips'>
                            <li><code>#{$wrap}</code> <?php _e("converts to the ID selector of the menu wrapper, e.g. div#mega-menu-wrap-primary-14", "megamenu"); ?></li>
                            <li><code>#{$menu}</code> <?php _e("converts to the ID selector of the menu, e.g. ul#mega-menu-primary-1", "megamenu"); ?></li>
                            <li><?php _e("Use @import rules to import CSS from other plugins or your theme directory, e.g:"); ?>
                            <br /><br /><code>#{$wrap} #{$menu} {<br />&nbsp;&nbsp;&nbsp;&nbsp;@import "shortcodes-ultimate/assets/css/box-shortcodes.css";<br />}</code></li>
                        </ul>
                    </td>
                </tr>

            </table>

            <?php

            submit_button();

            ?>

            <?php if ( $this->string_contains( $this->id, array("custom") ) ) : ?>

                <a class='delete confirm' href='<?php echo wp_nonce_url(admin_url("admin-post.php?action=megamenu_delete_theme&theme_id={$this->id}"), 'megamenu_delete_theme') ?>'><?php _e("Delete Theme", "megamenu"); ?></a>

            <?php else : ?>

                <a class='revert confirm' href='<?php echo wp_nonce_url(admin_url("admin-post.php?action=megamenu_revert_theme&theme_id={$this->id}"), 'megamenu_revert_theme') ?>'><?php _e("Revert Changes", "megamenu"); ?></a>

            <?php endif; ?>

            <a class='duplicate' href='<?php echo wp_nonce_url(admin_url("admin-post.php?action=megamenu_duplicate_theme&theme_id={$this->id}"), 'megamenu_duplicate_theme') ?>'><?php _e("Duplicate Theme", "megamenu"); ?></a>

            </form>

        <?php

    }

    /**
     * Print an arrow dropdown selection box
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_arrow_option( $key ) {

        $value = $this->active_theme[$key];
        
        $arrow_icons = $this->arrow_icons(); 

        ?>
            <select class='icon_dropdown' name='settings[<?php echo $key ?>]'>

                <?php 

                    echo "<option value='disabled'>" . __("Disabled", "megamenu") . "</option>";

                    foreach ($arrow_icons as $code => $class) {
                        $name = str_replace('dashicons-', '', $class);
                        $name = ucwords(str_replace(array('-','arrow'), ' ', $name));
                        echo "<option data-class='{$class}' value='{$code}' " . selected( $value == $code ) . ">{$name}</option>";
                    }

                ?>
            </select>
            <span class="selected_icon <?php echo $arrow_icons[$value] ?>"></span>


        <?php
    }

    /**
     * Print a colorpicker
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_color_option( $key ) {

        $value = $this->active_theme[$key];

        if ( $value == 'transparent' ) {
            $value = 'rgba(0,0,0,0)';
        }

        if ( $value == 'rgba(0,0,0,0)' ) {
            $value_text = 'transparent';
        } else {
            $value_text = $value;
        }

        echo "<div class='mm-picker-container'>";
        echo "    <input type='text' class='mm_colorpicker' name='settings[$key]' value='{$value}' />";
        echo "    <div class='chosen-color'>{$value_text}</div>";
        echo "</div>";

    }


    /**
     * Print a font weight selector
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_weight_option( $key ) {

        $value = $this->active_theme[$key];

        echo "<select name='settings[$key]'>";
        echo "    <option value='normal' " . selected( $value, 'normal', true) . ">" . __("Normal", "megamenu") . "</option>";
        echo "    <option value='bold'"    . selected( $value, 'bold', true) . ">" . __("Bold", "megamenu") . "</option>";
        echo "</select>";

    }


    /**
     * Print a font transform selector
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_transform_option( $key ) {

        $value = $this->active_theme[$key];

        echo "<select name='settings[$key]'>";
        echo "    <option value='none' "      . selected( $value, 'none', true) . ">" . __("Normal", "megamenu") . "</option>";
        echo "    <option value='capitalize'" . selected( $value, 'capitalize', true) . ">" . __("Capitalize", "megamenu") . "</option>";
        echo "    <option value='uppercase'"  . selected( $value, 'uppercase', true) . ">" . __("Uppercase", "megamenu") . "</option>";
        echo "    <option value='lowercase'"  . selected( $value, 'lowercase', true) . ">" . __("Lowercase", "megamenu") . "</option>";
        echo "</select>";

    }


    /**
     * Print a textarea
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_textarea_option( $key ) {

        $value = $this->active_theme[$key];

        echo "<textarea id='codemirror' name='settings[$key]'>" . stripslashes( $value ) . "</textarea>";

    }


    /**
     * Print a font selector
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_font_option( $key ) {

        $value = $this->active_theme[$key];

        echo "<select name='settings[$key]'>";

        echo "<option value='inherit'>" . __("Theme Default", "megamenu") . "</option>";

        foreach ( $this->fonts() as $font ) {
            $parts = explode(",", $font);
            $font_name = trim($parts[0]);
            echo "<option value=\"{$font}\" " . selected( $font, $value ) . ">{$font_name}</option>";
        }

        echo "</select>";
    }


    /**
     * Print a text input
     *
     * @since 1.0
     * @param string $key
     * @param string $value
     */
    public function print_theme_freetext_option( $key ) {

        $value = $this->active_theme[$key];

        echo "<input type='text' name='settings[$key]' value='{$value}' />";

    }


    /**
     * Returns a list of available fonts.
     *
     * @since 1.0
     */
    public function fonts() {

        $fonts = array(
            "Georgia, serif",
            "Palatino Linotype, Book Antiqua, Palatino, serif",
            "Times New Roman, Times, serif",
            "Arial, Helvetica, sans-serif",
            "Arial Black, Gadget, sans-serif",
            "Comic Sans MS, cursive, sans-serif",
            "Impact, Charcoal, sans-serif",
            "Lucida Sans Unicode, Lucida Grande, sans-serif",
            "Tahoma, Geneva, sans-serif",
            "Trebuchet MS, Helvetica, sans-serif",
            "Verdana, Geneva, sans-serif",
            "Courier New, Courier, monospace",
            "Lucida Console, Monaco, monospace"
        );

        $fonts = apply_filters( "megamenu_fonts", $fonts );

        return $fonts;

    }


    /**
     * List of all available arrow DashIcon classes.
     *
     * @since 1.0
     * @return array - Sorted list of icon classes
     */
    private function arrow_icons() {

        $icons = array(
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
        );

        $icons = apply_filters( "megamenu_arrow_icons", $icons );

        return $icons;
        
    }

    /**
     * Enqueue required CSS and JS for Mega Menu
     *
     * @since 1.0
     */
    public function enqueue_theme_editor_scripts( $hook ) {

        if( 'appearance_page_megamenu_theme_editor' != $hook )
            return;

        wp_enqueue_style( 'spectrum', MEGAMENU_BASE_URL . 'js/spectrum/spectrum.css', false, MEGAMENU_VERSION );
        wp_enqueue_style( 'mega-menu-theme-editor', MEGAMENU_BASE_URL . 'css/theme-editor.css', false, MEGAMENU_VERSION );
        wp_enqueue_style( 'codemirror', MEGAMENU_BASE_URL . 'js/codemirror/codemirror.css', false, MEGAMENU_VERSION );

        wp_enqueue_script( 'spectrum', MEGAMENU_BASE_URL . 'js/spectrum/spectrum.js', array( 'jquery' ), MEGAMENU_VERSION );
        wp_enqueue_script( 'codemirror', MEGAMENU_BASE_URL . 'js/codemirror/codemirror.js', array(), MEGAMENU_VERSION );
        wp_enqueue_script( 'mega-menu-theme-editor', MEGAMENU_BASE_URL . 'js/theme-editor.js', array('jquery', 'spectrum', 'codemirror'), MEGAMENU_VERSION );

        wp_localize_script( 'mega-menu-theme-editor', 'megamenu_theme_editor',
            array(
                'confirm' => __("Are you sure?", "megamenu")
            )
        );
    }

}

endif;