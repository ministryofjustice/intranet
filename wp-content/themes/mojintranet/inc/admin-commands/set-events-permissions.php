<?php
namespace MOJ_Intranet\Admin_Commands;
class Set_Events_Permissions extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Set Event Permissions';
    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Set event so they can be managed';
    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {

        $roles = [
            'administrator',
            'editor',
            'agency-editor',
            'regional-editor'
        ];

        $add_capabilities = [
            'read_event',
            'edit_events',
            'edit_event',
            'edit_others_events',
            'edit_published_events',
            'publish_events',
            'delete_event',
            'delete_others_events'
        ];

        foreach ($roles as $role) {
            $wpRole = get_role($role);

            foreach ($add_capabilities as $cap) {
                $wpRole->add_cap($cap);
            }
        }

        echo '<p>Events Permissions Set</p>';
    }
}
