<?php

namespace MOJ_Intranet\Admin_Commands;

class Generic_Template_Move_Part3 extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Generic Template Move Part 3 - Regional pages';
    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Move All regional pages using Generic Nav to Generic';
    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

            $page_query = "SELECT id FROM $wpdb->posts
                   WHERE post_type = 'regional_page'
                   AND $wpdb->posts.ID  IN
                        (SELECT post_id FROM $wpdb->postmeta 
                         WHERE meta_key = 'dw_regional_template'
                         AND meta_value = 'page_generic_nav.php'
                        )
                  ";

            $pages = $wpdb->get_results($wpdb->prepare($page_query));

            foreach ($pages as $page) {
                echo $page->id . ' Updated <br/>';

                update_post_meta($page->id, 'dw_lhs_menu_on', '1');
                update_post_meta($page->id, 'dw_regional_template', 'page_generic.php');
            }
    }

}
