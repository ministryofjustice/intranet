<?php

namespace MOJIntranet\Taxonomies;

class Agency extends Taxonomy
{
    protected $name = 'agency';

    protected $objectType = array(
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

    public function __construct()
    {
        parent::__construct();

        if (current_user_can('manage_agencies')) {
            add_action('admin_menu', array($this, 'addAdminMenuItem'));
        }

        if (current_user_can('assign_agencies_to_posts')) {
            // Show form fields to edit user agency
            // Using priority 9 here to bump it above "More fields" section
            add_action('show_user_profile', array($this, 'editUserProfile'), 9);
            add_action('edit_user_profile', array($this, 'editUserProfile'), 9);

            // Update the agency terms when the edit user page is updated
            add_action('personal_options_update', array($this, 'editUserProfileSave'));
            add_action('edit_user_profile_update', array($this, 'editUserProfileSave'));
        }
        
        
    }

    public function addAdminMenuItem()
    {
        add_submenu_page('users.php', 'Agencies', 'Agencies', 'administrator', 'edit-tags.php?taxonomy=agency&post_type=user');
    }

    /**
     * Adds an additional settings section on the edit user/profile page in the admin.  This section allows users to
     * select a profession from a checkbox of terms from the profession taxonomy.  This is just one example of
     * many ways this can be handled.
     *
     * @param object $user The user object currently being edited.
     */
    public function editUserProfile($user)
    {
        $terms = get_terms($this->name, array(
            'hide_empty' => false,
        ));

        ?>

        <h3><?php _e('Agencies'); ?></h3>

        <table class="form-table">

            <tr>
                <th><label for="agency"><?php _e('Agencies for Editor'); ?></label></th>

                <td>
                    <p class="description">Select agencies that this user is able to edit content for. Only applies to the Agency Editor role.</p>
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
    public function editUserProfileSave($user_id) {
        $term = esc_attr( $_POST['agency'] );

        $agencies = $_POST['agency'];
        if (!is_array($agencies)) {
            $agencies = array();
        }
        $agencies = array_map('sanitize_text_field', $agencies);

        /* Sets the terms for the user. */
        wp_set_object_terms($user_id, $agencies, 'agency', false);

        clean_object_term_cache($user_id, 'agency');
    }
}
