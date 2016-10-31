<?php
namespace MOJ_Intranet\Admin_Commands;
class Set_Admin_Regional_Permissions extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Set Admin Regional Content Permissions';
    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Set Admin permissions so they can manage regional content';
    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {

        $wpRole = get_role('administrator');

        $add_capabilities = [
            'read_regional_page',
            'edit_regional_page',
            'edit_others_regional_pages',
            'edit_published_regional_pages',
            'publish_regional_pages',
            'delete_regional_page',
            'delete_others_regional_pages',
            'read_regional_news',
            'edit_regional_news',
            'edit_others_regional_news',
            'edit_published_regional_news',
            'publish_regional_news',
            'delete_regional_news',
            'delete_others_regional_news'
        ];

        foreach ($add_capabilities as $cap) {
            $wpRole->add_cap($cap);
        }

        echo '<p>Admin Regional Content Permissions Set</p>';
    }
}
