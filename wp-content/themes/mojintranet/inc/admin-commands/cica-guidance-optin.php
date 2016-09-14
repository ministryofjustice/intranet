<?php

namespace MOJ_Intranet\Admin_Commands;

class CICA_Guidance_Optin extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'CICA Guidance Opt-in';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Opt-in CICA to all HQ Guidance pages below the HQ main Guidance page';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        $guidance_page = get_page_by_path('guidance');

        if (is_null($guidance_page) == false) {

            $pages = $wpdb->get_results('SELECT id, post_title FROM wp_posts WHERE post_type = "page" AND post_parent = ' . $guidance_page->ID  );

            foreach ($pages as $page) {
                $this->find_child_pages($page->id);
                if(has_term('hq', 'agency', $page->id)) {
                    $this->opt_in_page($page->id);
                }
            }

        }


    }

    /**
     * Finds Child Pages of a parent page
     *
     * @param int $parent_page_id the id of the parent page
     *
     * @return void
     */
    public function find_child_pages($parent_page_id) {
        global $wpdb;

        $child_pages = $wpdb->get_results('SELECT id, post_title FROM wp_posts WHERE post_type = "page" AND post_parent = ' . $parent_page_id  );

        if (count($child_pages) > 0) {
            foreach ($child_pages as $child_page) {
                $this->find_child_pages($child_page->id);
                if(has_term('hq', 'agency', $child_page->id)) {
                    $this->opt_in_page($child_page->id);
                }
            }
        }

    }

    /**
     * Opts in page for CICA
     *
     * @param int $page_id the id of the page to opt-in
     *
     * @return void
     */
    public function opt_in_page($page_id) {
        wp_set_object_terms($page_id, 'cica', 'agency', true);
        echo 'Opted in Page - ' . get_the_title($page_id) . ' ['.$page_id.'] <br/>';
    }
}
