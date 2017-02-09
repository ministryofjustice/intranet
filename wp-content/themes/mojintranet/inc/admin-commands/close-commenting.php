<?php

namespace MOJ_Intranet\Admin_Commands;

class Close_Commenting extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Close Commenting';
    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Close Commenting on all posts';
    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        $wpdb->query(
            "UPDATE  $wpdb->posts SET comment_status = 'closed' "
        );

    }

}
