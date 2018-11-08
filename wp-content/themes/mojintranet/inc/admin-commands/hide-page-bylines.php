<?php

namespace MOJ_Intranet\Admin_Commands;

class Hide_Page_Bylines extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Hide Bylines for All Pages';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Hides all page bylines';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        $pages = $wpdb->get_results('SELECT id, post_title FROM wp_posts WHERE post_type = "page"');

        echo '<ul>';

        foreach ($pages as $page) {
            $hide_byline = get_post_meta($page->id, 'dw_hide_page_details', true);

            if ($hide_byline != '1') {
                update_post_meta($page->id, 'dw_hide_page_details', '1');
                echo '<li>Hiding byline for page: "' . $page->post_title . '" (' . $page->id . ')</li>';
            }
            else {
                echo '<li>Skipped for page: "' . $page->post_title . '" (' . $page->id . ')</li>';
            }
        }

        echo '</ul>';

    }
}
