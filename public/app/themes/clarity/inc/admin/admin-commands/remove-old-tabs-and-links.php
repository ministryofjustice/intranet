<?php
namespace MOJ_Intranet\Admin_Commands;

class RemoveOldTabsAndLinks extends AdminCommand
{
    /**
     * Name of the command.
     *
     * @var ?string
     */
    public ?string $name = 'Remove Old Tabs and Links';

    /**
     * Description of what this command will do.
     *
     * @var ?string
     */
    public ?string $description = 'Remove Tabs and Links using the old postmeta structure ';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute(): void
    {
        global $wpdb;

        $wpdb->query(
            "DELETE FROM $wpdb->postmeta
		     WHERE meta_key LIKE '_quick_links%' 
		     OR meta_key LIKE '_content_tabs%'  
		   "
        );
    }
}
