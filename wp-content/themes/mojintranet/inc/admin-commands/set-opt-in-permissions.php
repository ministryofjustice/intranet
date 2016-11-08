<?php
namespace MOJ_Intranet\Admin_Commands;
class Set_Opt_In_Permissions extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Set Opt in Permissions';
    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Set HQ Opt in permissions for specific roles';
    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {

        $roles = [
            'administrator',
            'editor',
            'agency-editor'
        ];

        foreach ($roles as $role) {
            $wpRole = get_role($role);
            $wpRole->add_cap('opt_in_content');
        }

        echo '<p>Opt In Permissions Set</p>';
    }
}
