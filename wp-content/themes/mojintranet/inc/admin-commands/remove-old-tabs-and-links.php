<?php

namespace MOJ_Intranet\Admin_Commands;

class Remove_Old_Tabs_And_Links extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Remove Old Tabs and Links';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Remove Tabs and Links using the old postmeta structure ';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        $wpdb->query(
            "DELETE FROM $wpdb->postmeta
		     WHERE meta_key LIKE '_quick_links%' 
		     OR meta_key LIKE '_content_tabs%'  
		   "
        );

    }
}
