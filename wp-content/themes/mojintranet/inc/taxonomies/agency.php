<?php

namespace MOJ_Intranet\Taxonomies;

use Agency_Context;
use Agency_Editor;

class Agency extends Taxonomy {
    protected $name = 'agency';

    protected $object_types = array(
        'user',
        'news',
        'post',
        'page',
        'webchat',
        'event',
        'document',
        'snippet',
    );

    protected $args = array(
        'labels' => array(
            'name' => 'Agencies',
            'singular_name' => 'Agency',
            'menu_name' => 'Agencies',
            'all_items' => 'All Agencies',
            'parent_item' => 'Parent Agency',
            'parent_item_colon' => 'Parent Agency:',
            'new_item_name' => 'New Agency Name',
            'add_new_item' => 'Add New Agency',
            'edit_item' => 'Edit Agency',
            'update_item' => 'Update Agency',
            'separate_items_with_commas' => 'Separate Agencies with commas',
            'search_items' => 'Search Agencies',
            'add_or_remove_items' => 'Add or remove Agencies',
            'choose_from_most_used' => 'Choose from the most used Agencies',
            'not_found' => 'Not Found',
        ),
        'hierarchical' => true,
        'public' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'show_tagcloud' => false,
        'rewrite' => false,
        'capabilities' => array(
            'manage_terms' => 'manage_agencies',
            'edit_terms' => 'manage_agencies',
            'delete_terms' => 'manage_agencies',
            'assign_terms' => 'assign_agencies_to_posts',
        ),
    );

    public function __construct() {
        parent::__construct();

        if (current_user_can('manage_agencies')) {
            add_action('admin_menu', array($this, 'add_admin_menu_item'));
        }

        if (current_user_can('assign_agencies_to_posts')) {
            // Show form fields to edit user agency
            // Using priority 9 here to bump it above "More fields" section
            add_action('show_user_profile', array($this, 'edit_user_profile'), 9);
            add_action('edit_user_profile', array($this, 'edit_user_profile'), 9);
            add_action('user_new_form', array($this, 'edit_user_profile'), 9);

            // Update the agency terms when the edit user page is updated
            add_action('personal_options_update', array($this, 'edit_user_profile_save'));
            add_action('edit_user_profile_update', array($this, 'edit_user_profile_save'));
            add_action('user_register', array($this, 'edit_user_profile_save'));
        }

        if (!current_user_can('assign_agencies_to_posts')) {
            // Remove agency meta box
            add_action('admin_menu', array($this, 'remove_agency_meta_box'));
        }

        if (Agency_Context::current_user_can_have_context()) {
            // Post filtering
            add_filter('parse_query', array($this, 'filter_posts_by_agency'));
            add_filter('restrict_manage_posts', array($this, 'add_agency_filter'));

            // Auto-tag agency
            add_action('save_post', array($this, 'set_agency_terms_on_save_post'));

            // Capabilities
            add_action('map_meta_cap', array($this, 'restrict_edit_post_to_current_agency'), 10, 4);

            // Quick actions
            add_action('page_row_actions', array($this, 'add_opt_in_out_quick_actions'), 10, 2);
            add_action('post_row_actions', array($this, 'add_opt_in_out_quick_actions'), 10, 2);
            add_action('load-post.php', array($this, 'quick_action_opt_in_out'));
        }
    }

    public function add_admin_menu_item() {
        add_submenu_page('users.php', 'Agencies', 'Agencies', 'administrator', 'edit-tags.php?taxonomy=agency&post_type=user');
    }

    /**
     * Adds an additional settings section on the edit user/profile page in the admin.  This section allows users to
     * select a profession from a checkbox of terms from the profession taxonomy.  This is just one example of
     * many ways this can be handled.
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

        <h3><?php _e('Agencies'); ?></h3>

        <table class="form-table">

            <tr>
                <th><label for="agency"><?php _e('Agencies for Editor'); ?></label></th>

                <td>
                    <p class="description">Select agencies that this user is able to edit content for. Only applies to
                        the Agency Editor role.</p>
                    <?php

                    // If there are any agency terms, loop through them and display checkboxes.
                    if (!empty($terms)) {

                        foreach ($terms as $term) { ?>
                            <input type="checkbox" name="agency[]" id="agency-<?php echo esc_attr($term->slug); ?>"
                                   value="<?php echo esc_attr($term->slug); ?>" <?php $user && checked(true, is_object_in_term($user->ID, 'agency', $term->slug)); ?> />
                            <label for="agency-<?php echo esc_attr($term->slug); ?>"><?php echo $term->name; ?></label>
                            <br/>
                        <?php }
                    } /* If there are no agency terms, display a message. */
                    else {
                        _e('There are no agencies to choose from.');
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
        $agencies = $_POST['agency'];
        if (!is_array($agencies)) {
            $agencies = array();
        }
        $agencies = array_map('sanitize_text_field', $agencies);

        /* Sets the terms for the user. */
        wp_set_object_terms($user_id, $agencies, 'agency', false);

        clean_object_term_cache($user_id, 'agency');
    }

    /**
     * Add agency filters to post listing pages.
     */
    public function add_agency_filter() {
        global $typenow, $pagenow;

        $is_correct_post_type = in_array($typenow, $this->object_types);
        $is_correct_page = ($pagenow == 'edit.php');
        $is_hq_user = (Agency_Context::get_agency_context() == 'hq');

        if (!$is_correct_post_type || !$is_correct_page || $is_hq_user) {
            return;
        }

        $is_checked = ( isset($_GET['show-hq-posts']) && $_GET['show-hq-posts'] == '1' );
        ?>
        <label class="agency-filter-filter">
            <input type="checkbox" name="show-hq-posts" value="1" <?php checked(true, $is_checked); ?> />
            Show HQ content
        </label>
        <?php
    }

    /**
     * Add taxonomy filter to the WP_Query object used for displaying posts
     * on the page.
     *
     * @param \WP_Query $query
     * @return mixed
     */
    public function filter_posts_by_agency(\WP_Query $query) {
        global $typenow, $pagenow;

        $is_correct_post_type = in_array($typenow, $this->object_types);
        $is_correct_page = ( $pagenow == 'edit.php' );
        $user_can_have_context = Agency_Context::current_user_can_have_context();

        if (!$is_correct_post_type || !$is_correct_page || !$user_can_have_context) {
            return $query;
        }

        // Define the agency taxonomy filter
        $agency = array(Agency_Context::get_agency_context());

        // Show HQ posts?
        if (isset($_GET['show-hq-posts']) && $_GET['show-hq-posts'] == '1') {
            $agency[] = 'hq';
        }

        $query->query_vars['agency'] = $agency;

        return $query;
    }

    /**
     * @param int $post_id
     */
    public function set_agency_terms_on_save_post($post_id) {
        $post_type = get_post_type($post_id);
        if (
            !in_array($post_type, $this->object_types) ||
            !Agency_Context::current_user_can_have_context()
        ) {
            return;
        }

        $terms = wp_get_object_terms($post_id, 'agency');

        if (empty($terms)) {
            $agency_context = Agency_Context::get_agency_context();
            wp_set_object_terms($post_id, $agency_context, 'agency');
        }
    }

    /**
     * Remove agency meta box from post edit pages.
     */
    public function remove_agency_meta_box() {
        foreach ($this->object_types as $object) {
            remove_meta_box('agencydiv', $object, 'normal');
        }
    }

    /**
     * Stop users from editing posts that belong to agencies which are not
     * the current agency context.
     *
     * @param $caps
     * @param $cap
     * @param $user_id
     * @param $args
     *
     * @return array
     */
    public function restrict_edit_post_to_current_agency($caps, $cap, $user_id, $args) {
        if ($cap !== 'edit_post' && $cap !== 'delete_post') {
            // Not relevant, return early.
            return $caps;
        }

        $post_id = $args[0];
        $post_type = get_post_type($post_id);

        if (!in_array($post_type, $this->object_types)) {
            // Not relevant, return early.
            return $caps;
        }

        $owner = Agency_Editor::get_post_agency($post_id);
        $context = Agency_Context::get_agency_context();

        if ($owner !== $context) {
            // User does not have permission to edit this post
            $caps[] = 'do_not_allow';
        }

        return $caps;
    }

    public function add_opt_in_out_quick_actions($actions, $post) {
        $is_opted_in = Agency_Editor::is_post_opted_in($post->ID);

        if (
            is_null($is_opted_in) || // User cannot opt-in to this post.
            $post->post_status !== 'publish' // The post is not published.
        ) {
            return $actions;
        }

        if ($is_opted_in) {
            $action = 'opt-out';
        } else {
            $action = 'opt-in';
        }

        $url = admin_url('post.php');
        $url = add_query_arg(array(
            'post' => $post->ID,
            'action' => $action,
        ), $url);
        $url = wp_nonce_url($url, 'opt_in_out-post_' . $post->ID);

        if ($is_opted_in) {
            $actions['opt_out'] = '<a href="' . $url . '" title="' . esc_attr__( 'Opt-out of this post' ) . '">' . _x( 'Opt-out', 'verb' ) . '</a>';
        } else {
            $actions['opt_in'] = '<a href="' . $url . '" title="' . esc_attr__( 'Opt-in to this post' ) . '">' . _x( 'Opt-in', 'verb' ) . '</a>';
        }

        return $actions;
    }

    public function quick_action_opt_in_out() {
        if (
            !isset($_GET['action']) ||
            !in_array($_GET['action'], array('opt-in', 'opt-out')) ||
            !isset($_GET['post'])
        ) {
            return false;
        }

        $post_id = $_GET['post'];

        if (
            !isset($_GET['_wpnonce']) ||
            !wp_verify_nonce($_GET['_wpnonce'], 'opt_in_out-post_' . $post_id)
        ) {
            wp_die('Missing or invalid nonce.');
        }

        $post_type = get_post_type($post_id);
        if (in_array($post_type, $this->object_types)) {
            $action = $_GET['action'];
            $agency = Agency_Context::get_agency_context();
            $terms = [];
            $current_terms = wp_get_post_terms($post_id, 'agency');

            //wp_get_post_terms doesn't have the option to return just term slugs
            foreach ($current_terms as $term) {
                $terms[] = $term->slug;
            }

            if ($action == 'opt-in') {
                $terms[] = $agency;
            }
            else {
                if (($key = array_search($agency, $terms)) !== false) {
                    unset($terms[$key]);
                }
            }

            wp_set_object_terms($post_id, $terms, 'agency');

            wp_redirect(wp_get_referer());
            exit;
        }
    }
}
