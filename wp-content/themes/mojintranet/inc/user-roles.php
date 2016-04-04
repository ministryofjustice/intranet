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

namespace MOJIntranet;

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

        // Add a new role with editor caps
        $agencyEditor = $this->wp_roles->add_role('agency-editor', 'Agency Editor', $editor->capabilities);
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
