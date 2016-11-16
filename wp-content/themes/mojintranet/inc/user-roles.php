<?php

/**
 * Configure user roles for personalisation.
 *
 * Agency Editor:
 *  - able to edit any content belonging to their agency
 *
 * Admin Editor:
 *  - able to edit any content belonging to any agency
 *
 */

namespace MOJ_Intranet;

use WP_Roles;

class UserRoles {
    /**
     * @var WP_Roles
     */
    protected $wp_roles = null;

    public function __construct()
    {
        // Setup WP_Roles object
        global $wp_roles;
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        $this->wp_roles = $wp_roles;

        $this->initRoles();
        $this->initCapabilities();
    }

    public function initRoles() {
        // Add agency-editor role if required
        if (!$this->roleExists('agency-editor')) {
            $this->addAgencyEditorRole();
        }

        // Add regional-editor role if required
        if (!$this->roleExists('regional-editor')) {
            $this->addRegionalEditorRole();
        }

        // Rename editor role if required
        $editorName = 'Global Editor';
        if ($this->roleName('editor') !== $editorName) {
            $this->renameRole('editor', $editorName);
        }
    }

    public function initCapabilities() {
        $assignCaps = array(
            'editor' => array(
                'assign_agencies_to_posts',
            ),
            'administrator' => array(
                'assign_agencies_to_posts',
                'manage_agencies',
            ),
        );

        foreach ($assignCaps as $role => $caps) {
            $wpRole = $this->wp_roles->get_role($role);
            foreach ($caps as $cap) {
                if (!$wpRole->has_cap($cap)) {
                    $wpRole->add_cap($cap);
                }
            }
        }
    }

    /**
     * Add new role Agency Editor.
     * Inherit capabilities from Editor role.
     */
    public function addAgencyEditorRole() {
        $editor = $this->wp_roles->get_role('editor');
        
        $editor->capabilities["assign_agencies_to_posts"] = false;

        // Add a new role with editor caps
        $agencyEditor = $this->wp_roles->add_role('agency-editor', 'Agency Editor', $editor->capabilities);
    }

    /**
     * Add new role Regional Editor.
     * Inherit capabilities from Editor role.
     */
    public function addRegionalEditorRole() {
        $editor = $this->wp_roles->get_role('editor');

        $editor->capabilities["edit_posts"] = false;
        $editor->capabilities["edit_others_posts"] = false;
        $editor->capabilities["edit_published_posts"] = false;
        $editor->capabilities["manage_categories"] = false;
        $editor->capabilities["edit_theme_options"] = false;
        $editor->capabilities["assign_agencies_to_posts"] = false;
        $editor->capabilities["edit_pages"] = false;
        $editor->capabilities["edit_others_pages"] = false;
        $editor->capabilities["edit_published_pages"] = false;

        $editor->capabilities["read_regional_page"] = true;
        $editor->capabilities["edit_regional_pages"] = true;
        $editor->capabilities["edit_regional_page"] = true;
        $editor->capabilities["edit_others_regional_pages"] = true;
        $editor->capabilities["edit_published_regional_pages"] = true;
        $editor->capabilities["publish_regional_pages"] = true;
        $editor->capabilities["delete_regional_page"] = true;
        $editor->capabilities["delete_others_regional_pages"] = true;

        $editor->capabilities["read_regional_news"] = true;
        $editor->capabilities["edit_regional_news"] = true;
        $editor->capabilities["edit_others_regional_news"] = true;
        $editor->capabilities["edit_published_regional_news"] = true;
        $editor->capabilities["publish_regional_news"] = true;
        $editor->capabilities["delete_regional_news"] = true;
        $editor->capabilities["delete_others_regional_news"] = true;

        // Add a new role with editor caps
        $regionalEditor = $this->wp_roles->add_role('regional-editor', 'Regional Editor', $editor->capabilities);
    }

    /**
     * Check if role exists
     *
     * @param $role
     * @return bool
     */
    public function roleExists($role) {
        $obj = $this->wp_roles->get_role($role);
        return !is_null($obj);
    }

    /**
     * Get the name of a role
     *
     * @param $role
     * @return bool
     */
    public function roleName($role) {
        $names = $this->wp_roles->get_names();
        if (isset($names[$role])) {
            return $names[$role];
        } else {
            return false;
        }
    }

    /**
     * Rename a user role
     *
     * @param $role
     * @param $name
     */
    public function renameRole($role, $name) {
        $this->wp_roles->roles[$role]['name'] = $name;
        $this->wp_roles->role_names[$role] = $name;
    }
}

new UserRoles();
