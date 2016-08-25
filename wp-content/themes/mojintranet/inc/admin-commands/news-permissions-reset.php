<?php

namespace MOJ_Intranet\Admin_Commands;

class News_Permissions_Reset extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'News CPT Permissions Reset';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Reset permissions for News CPT';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        $roles = ['administrator', 'agency-editor', 'regional-editor', 'editor'];

        $caps = [ 
            'edit_news',
            'edit_others_news',
            'read_news',
            'read_private_news',
            'delete_news',
            'publish_news',
        ];

        foreach ($roles as $role) {
            $wpRole = get_role($role);
            foreach ($caps as $cap) {
               $wpRole->add_cap($cap);
            }
        }
        echo '<p>News Permissions reset</p>';
    }
}
