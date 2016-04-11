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

            // Update the agency terms when the edit user page is updated
            add_action('personal_options_update', array($this, 'edit_user_profile_save'));
            add_action('edit_user_profile_update', array($this, 'edit_user_profile_save'));
        }

        if (Agency_Context::current_user_can_have_context()) {
            add_filter('parse_query', array($this, 'filter_posts_by_agency'));
            add_filter('restrict_manage_posts', array($this, 'add_agency_filter'));
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
                                   value="<?php echo esc_attr($term->slug); ?>" <?php checked(true, is_object_in_term($user->ID, 'agency', $term->slug)); ?> />
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
     * Add filters to post listing pages.
     */
    public function add_agency_filter() {
        global $typenow, $pagenow;
        if (!in_array($typenow, $this->object_types) || $pagenow !== 'edit.php') {
            return;
        }

        $context = Agency_Editor::get_agency_by_slug(Agency_Context::get_agency_context());

        $agencies = array(
            'hq' => 'HQ',
            $context->slug => $context->name,
        );

        if (count($agencies) > 1):

        ?>
        <label style="margin-left: 5px;" class="agency-filter-label">Agency:</label>
        <?php

        foreach ($agencies as $slug => $name) {
            if (empty($_GET['agency'])) {
                $is_checked = true;
            } elseif (is_array($_GET['agency']) && in_array($slug, $_GET['agency'])) {
                $is_checked = true;
            } else {
                $is_checked = false;
            }
            ?>
            <label class="agency-filter-filter">
                <input type="checkbox" name="agency[]" value="<?php echo esc_attr($slug); ?>" <?php checked(true, $is_checked); ?> />
                <?php echo $name; ?>
            </label>
            <?php
        }

        endif;
    }

    public function filter_posts_by_agency($query) {
        global $typenow, $pagenow;

        if (
            !in_array($typenow, $this->object_types) ||
            $pagenow !== 'edit.php' ||
            !Agency_Context::current_user_can_have_context()
        ) {
            return $query;
        }

        if (isset($_GET['agency']) && is_array($_GET['agency'])) {
            $agency = $_GET['agency'];
        } else {
            $agency = array(
                'hq',
                Agency_Context::get_agency_context()
            );
        }

        $query->query_vars['agency'] = $agency;

        return $query;
    }
}
