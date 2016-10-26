<?php
namespace MOJ_Intranet\Admin_Commands;
class Set_Regional_Editor_Permissions extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Set Regional Editor Permissions';
    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Set Regional Editor permissions so they can manage regional content only';
    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {

        $wpRole = get_role('regional-editor');

        $remove_capabilities = [
            'edit_pages',
            'edit_others_pages',
            'edit_published_pages',
            'edit_news',
            'edit_others_news',
            'edit_published_news'
        ];
        foreach ($remove_capabilities as $cap) {
            $wpRole->remove_cap($cap);
        }

        $add_capabilities = [
            'edit_regional_page',
            'edit_others_regional_pages',
            'edit_published_regional_pages',
            'publish_regional_pages',
            'delete_regional_page',
            'delete_others_regional_pages',
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

        echo '<p>Regional Editor Permissions Set</p>';
    }
}
