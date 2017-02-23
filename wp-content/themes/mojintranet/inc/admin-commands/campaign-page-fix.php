<?php

namespace MOJ_Intranet\Admin_Commands;

class Campaign_Page_Fix extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Campaign Page Fix';
    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Fix Templates that are on old campaigjn template';
    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        $wpdb->query(
            "UPDATE  $wpdb->postmeta SET meta_value = 'page_generic.php'
		     WHERE meta_key = '_wp_page_template' 
		     AND meta_value = 'page_campaign.php'
		   "
        );

    }

}
