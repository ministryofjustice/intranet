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
    public $description = 'Remove Old Pods Data to cleanup site - Keywords kept for now';

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
		     OR meta_key LIKE 'user_grade' 
		     OR meta_key LIKE 'user_team'  
		     OR meta_key LIKE 'user_line_manager'  
		     OR meta_key LIKE 'user_telephone'  
		     OR meta_key LIKE 'user_mobile'  
		     OR meta_key LIKE 'user_working_pattern'  
		     OR meta_key LIKE 'user_key_skills' 
		     OR meta_key LIKE 'user_order' 
		   "
        );

        ?>
        <p>Cleaning User Data</p>

    <?php

    $wpdb->query(
    "DELETE FROM $wpdb->postmeta
    WHERE meta_key LIKE 'page_related_pages'
    OR meta_key LIKE 'page_related_tasks'
    OR meta_key LIKE 'related_stories'
    OR meta_key LIKE 'video_still'
    OR meta_key LIKE 'expiry_action'
    OR meta_key LIKE 'expiry_time'
    OR meta_key LIKE 'expiry_date'
    OR meta_key LIKE 'news_listing_type'
    "
    );

    ?>
    <p>Cleaning Page and News Meta Data</p>
    <?php

     $wpdb->query(
         "DELETE FROM $wpdb->options
            WHERE option_name LIKE 'general_intranet%'
            OR option_name LIKE 'homepage_control%'
            "
        );

        ?>
        <p>Cleaning Option Data</p>
        <?php

    }
}
