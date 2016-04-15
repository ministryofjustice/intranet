<?php

namespace MOJ_Intranet\Admin_Commands;

class Make_All_Global_Editors_In_HQ_Context extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Make All Global Editors In HQ Context';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Set the agency context to be HQ for all global editor users.';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        $users = new \WP_User_Query(array(
            'role' => 'editor',
        ));

        foreach ($users->results as $user) {
            echo '<p>Setting context for user: ' . $user->user_login . '</p>';
            update_user_meta($user->ID, 'agency_context', 'hq');
        }
    }
}
