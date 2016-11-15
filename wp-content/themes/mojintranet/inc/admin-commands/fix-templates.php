<?php

namespace MOJ_Intranet\Admin_Commands;

class Fix_Templates extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Fix Templates';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Fix Pages that may have been set to the regional template';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        $wpdb->query(
            "UPDATE $wpdb->postmeta
             SET meta_value = 'page_generic_nav.php'
		     WHERE meta_key LIKE '_wp_page_template' 
		     AND meta_value LIKE 'single-regional_page.php'  
		   "
        );

    }
}
