<?php

namespace MOJ_Intranet\Admin_Commands;

class Agency_Permissions_Fix extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Agency Permissions Fix';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Fix Agency Assign permission so Agency and Regional Editors can not assign agencies';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        $roles = ['agency-editor', 'regional-editor'];

        foreach ($roles as $role) {
            $wpRole = get_role($role);
            $wpRole->remove_cap('assign_agencies_to_posts');
        }
        echo '<p>Agency Permissions Fixed</p>';
    }
}
