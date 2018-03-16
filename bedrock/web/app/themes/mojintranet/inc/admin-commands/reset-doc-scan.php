<?php

namespace MOJ_Intranet\Admin_Commands;

class Reset_Doc_Scan extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Reset Doc Scan';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Reset the flags so pages can be rescanned for documents';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        $wpdb->query(
            "DELETE FROM $wpdb->postmeta
		     WHERE meta_key = 'related_docs_scanned' 
		   "
        );

    }
}
