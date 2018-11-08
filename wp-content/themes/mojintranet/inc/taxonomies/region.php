<?php

namespace MOJ_Intranet\Taxonomies;

use Region_Context;

class Region extends Taxonomy {
    protected $name = 'region';

    protected $object_types = array(
        'user',
        'regional_page',
        'regional_news',
        'document',
        'event'
    );

    protected $args = array(
        'labels' => array(
            'name' => 'Regions',
            'singular_name' => 'Region',
            'menu_name' => 'Regions',
            'all_items' => 'All Regions',
            'parent_item' => 'Parent Region',
            'parent_item_colon' => 'Parent Region:',
            'new_item_name' => 'New Region Name',
            'add_new_item' => 'Add New Region',
            'edit_item' => 'Edit Region',
            'update_item' => 'Update Region',
            'separate_items_with_commas' => 'Separate Regions with commas',
            'search_items' => 'Search Regions',
            'add_or_remove_items' => 'Add or remove Regions',
            'choose_from_most_used' => 'Choose from the most used Regions',
            'not_found' => 'Not Found',
        ),
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => false,
        'show_in_nav_menus' => false,
        'show_tagcloud' => false,
        'rewrite' => false,
        'capabilities' => array(
            'manage_terms' => 'manage_regions',
            'edit_terms' => 'manage_regions',
            'delete_terms' => 'manage_regions',
            'assign_terms' => 'assign_regions_to_posts',
        ),

    );

    public function __construct() {
        parent::__construct();

        if(current_user_can('create_users')) {
            add_action('admin_menu', array($this, 'add_admin_menu_item'));

            add_action('show_user_profile', array($this, 'edit_user_profile'), 9);
            add_action('edit_user_profile', array($this, 'edit_user_profile'), 9);
            add_action('user_new_form', array($this, 'edit_user_profile'), 9);

            // Update the agency terms when the edit user page is updated
            add_action('personal_options_update', array($this, 'edit_user_profile_save'));
            add_action('edit_user_profile_update', array($this, 'edit_user_profile_save'));
            add_action('user_register', array($this, 'edit_user_profile_save'));
        }
        else {
            // Remove Region Meta Box
            add_action('admin_menu', array($this, 'remove_region_meta_box'));
        }

        if (Region_Context::current_user_can_have_context()) {
            // Post filtering
            add_filter('parse_query', array($this, 'filter_posts_by_region'));

            // Auto-tag agency
            add_action('save_post', array($this, 'set_region_terms_on_save_post'));

            // Capabilities
            add_action('map_meta_cap', array($this, 'restrict_edit_post_to_current_region'), 10, 4);

        }


    }

    public function add_admin_menu_item() {
        add_submenu_page('users.php', 'Regions', 'Regions', 'administrator', 'edit-tags.php?taxonomy=region&post_type=user');
    }

    /**
     * Adds an additional settings section on the edit user/profile page in the admin.  This section allows users to
     * select a regions from checkboxes of terms from the region taxonomy.
     *
     * @param object $user The user object currently being edited.
     */
    public function edit_user_profile($user) {
        $terms = get_terms($this->name, array(
            'hide_empty' => false,
        ));

        if (
            is_string($user) &&
            in_array($user, array('add-existing-user', 'add-new-user'))
        ) {
            $user = false;
        }

        ?>

        <h3><?php _e('Regions'); ?></h3>

        <table class="form-table">

            <tr>
                <th><label for="agency"><?php _e('Regions for Editor'); ?></label></th>

                <td>
                    <p class="description">Select regions that this user is able to edit content for. Only applies to
                        the Region Editor role.</p>
                    <?php

                    // If there are any region terms, loop through them and display checkboxes.
                    if (!empty($terms)) {

                        foreach ($terms as $term) { ?>
                            <input type="checkbox" name="region[]" id="region-<?php echo esc_attr($term->slug); ?>"
                                   value="<?php echo esc_attr($term->slug); ?>" <?php $user && checked(true, is_object_in_term($user->ID, 'region', $term->slug)); ?> />
                            <label for="region-<?php echo esc_attr($term->slug); ?>"><?php echo $term->name; ?></label>
                            <br/>
                        <?php }
                    } /* If there are no regions terms, display a message. */
                    else {
                        _e('There are no regions to choose from.');
                    }

                    ?>
                </td>
            </tr>

        </table>

        <?php
    }

    /**
     * Saves the term selected on the edit user/profile page in the admin. This function is triggered when the page
     * is updated.  We just grab the posted data and use wp_set_object_terms() to save it.
     *
     * @param int $user_id The ID of the user to save the terms for.
     */
    public function edit_user_profile_save($user_id) {
        $regions = $_POST['region'];
        if (!is_array($regions)) {
            $regions = array();
        }
        $regions = array_map('sanitize_text_field', $regions);

        /* Sets the terms for the user. */
        wp_set_object_terms($user_id, $regions, 'region', false);

        clean_object_term_cache($user_id, 'region');
    }

    /**
     * Remove region meta box from post edit pages.
     */
    public function remove_region_meta_box() {
        foreach ($this->object_types as $object) {
            remove_meta_box('regiondiv', $object, 'side');
        }
    }

    /**
     * Add taxonomy filter to the WP_Query object used for displaying posts
     * on the page.
     *
     * @param \WP_Query $query
     * @return mixed
     */
    public function filter_posts_by_region(\WP_Query $query) {
        global $typenow, $pagenow;

        $is_correct_post_type = in_array($typenow, $this->object_types);
        $is_correct_page = ( $pagenow == 'edit.php' );
        $user_can_have_context = Region_Context::current_user_can_have_context();

        if (!$is_correct_post_type || !$is_correct_page || !$user_can_have_context) {
            return $query;
        }

        // Define the region taxonomy filter
        $region = array(Region_Context::get_region_context());

        $query->query_vars['region'] = $region;

        return $query;
    }

    /**
     * On save of post set the region of the content to the current region context
     * @param int $post_id
     */
    public function set_region_terms_on_save_post($post_id) {
        $post_type = get_post_type($post_id);
        if (
            !in_array($post_type, $this->object_types) ||
            !Region_Context::current_user_can_have_context()
        ) {
            return;
        }

        $terms = wp_get_object_terms($post_id, 'region');

        if (empty($terms)) {
            $region_context = Region_Context::get_region_context();
            wp_set_object_terms($post_id, $region_context, 'region');
        }
    }

    /**
     * Stop users from editing posts that belong to regions which are not
     * the current region context.
     *
     * @param $caps
     * @param $cap
     * @param $user_id
     * @param $args
     *
     * @return array
     */
    public function restrict_edit_post_to_current_region($caps, $cap, $user_id, $args) {
        $filter_caps = [
            'edit_regional_page',
            'delete_regional_page',
            'edit_regional_news',
            'delete_regional_news',
        ];

        if (!in_array($cap, $filter_caps) || !isset($args[0])) {
            // Not relevant, return early.
            return $caps;
        }

        $post_id = $args[0];
        $post_type = get_post_type($post_id);

        if (!in_array($post_type, $this->object_types)) {
            // Not relevant, return early.
            return $caps;
        }
        
        $context = Region_Context::get_region_context();
        
        if (!has_term($context, 'region', $post_id)) {
            // User does not have permission to edit this post
            $caps[] = 'do_not_allow';
        }

        return $caps;
    }
}
