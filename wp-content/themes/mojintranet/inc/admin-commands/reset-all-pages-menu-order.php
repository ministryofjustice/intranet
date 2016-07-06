<?php

namespace MOJ_Intranet\Admin_Commands;

class Reset_All_Pages_Menu_Order extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Reset All Pages Menu Order';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Resets all pages menu order';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {

        global $wpdb;

        $wpdb->query(
            "UPDATE $wpdb->posts
             SET menu_order = 0
             WHERE post_type ='page'
 		    "
        );

        echo '<p>All pages menu order has now been reset</p>';

    }
}
