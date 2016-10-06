<?php

namespace MOJ_Intranet\Admin_Commands;

class Update_Allday_Meta extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Update All Day meta for events';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Updates All Day meta for events as it no longer uses the allday string';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        $wpdb->query(
            "UPDATE $wpdb->postmeta
		     WHERE meta_key = '_event-allday' 
		     SET   meta_value = '1'
		   "
        );

    }
}
