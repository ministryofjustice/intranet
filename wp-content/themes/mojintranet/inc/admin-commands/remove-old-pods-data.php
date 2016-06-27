<?php

namespace MOJ_Intranet\Admin_Commands;

class Remove_Old_Pods_Data extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Remove Old Pods Data';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Remove Old Pods Data to cleanup site';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        $wpdb->query(
            "DELETE FROM $wpdb->usermeta
		     WHERE meta_key LIKE 'user_telephone' 
		     OR meta_key LIKE 'user_job_title' 
		     OR meta_key LIKE 'user_grade' 
		     OR meta_key LIKE 'user_team'  
		     OR meta_key LIKE 'user_line_manager'  
		     OR meta_key LIKE 'user_mobile'  
		     OR meta_key LIKE 'user_mobile'  
		     OR meta_key LIKE 'user_mobile'  
		   "
        );

        ?>
        <p>Cleaning User Data</p>



    }
}
