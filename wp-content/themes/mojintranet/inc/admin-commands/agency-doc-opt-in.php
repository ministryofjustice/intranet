<?php

namespace MOJ_Intranet\Admin_Commands;

class Agency_Doc_Opt_In extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Agency Doc Opt-in';
    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Opt-in a Agency to Documents Linked in HQ Pages that are Opted in';
    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;
        $total_docs = 0;;
        $agency = $_GET['agency'];

        if(strlen($agency) > 0) {
            $page_query = "SELECT id,post_title,post_content FROM $wpdb->posts
                   LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id )
                   LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id )
                   LEFT JOIN $wpdb->terms ON ( $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id )
                   WHERE post_type = 'page'
                   AND $wpdb->term_taxonomy.taxonomy = 'agency'
                   AND $wpdb->terms.slug = '%s'
                   AND $wpdb->posts.ID IN
                        (SELECT object_id FROM  $wpdb->term_relationships
                         LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id )
                         LEFT JOIN $wpdb->terms ON ( $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id )
                         WHERE $wpdb->terms.slug = 'hq'
                        )
                   AND $wpdb->posts.ID NOT IN
                        (SELECT post_id FROM $wpdb->postmeta 
                         WHERE meta_key = 'related_docs_scanned'
                         AND meta_value = 1
                        )
                  ";

            $pages = $wpdb->get_results($wpdb->prepare($page_query, $agency));

            foreach ($pages as $page) {
                $page_docs = [];

                $content = $page->post_content;
                $content .= $this->get_tab_content($page->id);

                $dom = new \DOMDocument;
                $dom->loadHTML($content);

                foreach ($dom->getElementsByTagName('a') as $node) {
                    if (strpos($node->getAttribute("href"), '/documents') === 0) {
                        $doc_id = url_to_postid($node->getAttribute("href"));
                        if (in_array($doc_id, $page_docs) == false) {
                            $page_docs[] = $doc_id;
                            wp_set_object_terms($doc_id, $agency, 'agency', true);
                        }
                    }
                }

                if (count($page_docs) > 0) {
                    update_post_meta($page->id, 'related_docs', implode(",", $page_docs));
                }
                update_post_meta($page->id, 'related_docs_scanned', 1);

                $total_docs += count($page_docs);
                echo 'Scanning page: ' . $page->post_title . ' [ID: ' . $page->id . '] - Document Links found [' . count($page_docs) . ']<br/>';
            }

            echo 'Total Docs Found: ' . $total_docs . '<br/>';
        }
        else {
            echo 'Please provide agency.';
        }
    }

    function get_tab_content($post_id) {
        $tab_content = '';
        $tab_num = get_post_meta($post_id, 'guidance_tabs', true);

        if (is_numeric($tab_num)) {
            for ($t = 0; $t < $tab_num; $t++) {
                $section_num = get_post_meta($post_id, 'guidance_tabs_'.$t.'_sections', true);

                if (is_numeric($section_num)) {
                    for ($s = 0; $s < $section_num; $s++) {
                      $tab_content .=  get_post_meta($post_id, 'guidance_tabs_' . $t . '_sections_' . $s . '_section_html_content', true);
                    }
                }

                $links_num = get_post_meta($post_id, 'guidance_tabs_'.$t.'_links', true);

                if (is_numeric($links_num)) {
                    for ($l = 0; $l < $links_num; $l++) {
                        $tab_link_url =  get_post_meta($post_id, 'guidance_tabs_' . $t . '_links_' . $l . '_link_url', true);

                        if (strpos($tab_link_url, '/documents') === 0) {
                            $tab_content .= '<a href="'.$tab_link_url .'" >Tab Link</a>';
                        }
                    }
                }
            }
        }

        return $tab_content;
    }


}
