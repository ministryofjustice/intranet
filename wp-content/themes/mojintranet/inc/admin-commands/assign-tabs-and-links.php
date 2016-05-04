<?php

namespace MOJ_Intranet\Admin_Commands;

class Assign_Tabs_And_Links extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Assign Tabs and Links';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Assigns Tabs and Links on Guidance Pages to the new postmeta structure ';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {

        $args = array(
            'post_type' => 'page',
            'posts_per_page' => -1,
        );
        $pages = get_posts($args);

        echo '<ul>';
        foreach ($pages as $page){
            if (get_post_meta($page->ID,'_wp_page_template',true) == 'page_guidance_and_support.php') {

                if (!metadata_exists('post', $page->ID, "guidance_tabs") ||  get_post_meta($page->ID, "guidance_tabs", true) == '0') { //check if tabs have been defined
                    //Find Links
                    $found_links_check = true;
                    $tab_links = array();
                    $count = 1;
                    while ($found_links_check == true) { //Presumed theres always text as there is no link count
                        if (metadata_exists('post', $page->ID, "_quick_links-link-text" . $count)) {
                            $type = 'link';
                            $show_tab1 = false;
                            $show_tab2 = false;
                            $url = '';

                            if (metadata_exists('post', $page->ID, "_quick_links-url" . $count)) {
                                $url = get_post_meta($page->ID, '_quick_links-url' . $count, true);
                            }

                            if (metadata_exists('post', $page->ID, "_quick_links-heading" . $count) && get_post_meta($page->ID, '_quick_links-heading' . $count, true) == 'on') {
                                $type = 'heading';
                            }

                            if (metadata_exists('post', $page->ID, "_quick_links-firsttab" . $count) && get_post_meta($page->ID, '_quick_links-firsttab' . $count, true) == 'on') {
                                $show_tab1 = true;
                            }

                            if (metadata_exists('post', $page->ID, "_quick_links-secondtab" . $count) && get_post_meta($page->ID, '_quick_links-secondtab' . $count, true) == 'on') {
                                $show_tab2 = true;
                            }

                            $tab_link = array(
                                'link_title' => get_post_meta($page->ID, '_quick_links-link-text' . $count, true),
                                'link_url' => $url,
                                'link_type' => $type,
                            );

                            if ($show_tab1) {
                                $tab_links[0][] = $tab_link;
                            }
                            if ($show_tab2) {
                                $tab_links[1][] = $tab_link;
                            }

                        } else {
                            $found_links_check = false;
                        }
                        $count++;
                    }

                    if (count($tab_links) > 0) {
                        foreach ($tab_links as $key => $tab) {
                            echo '<li>' . count($tab) . ' Links (Tab:' . ($key + 1) . ') found for page: "' . $page->post_title . '" (' . $page->ID . ')</li>';
                        }
                    }

                    //Find Tabs
                    $tab_count = get_post_meta($page->ID, '_content_tabs-tab-count', true);
                    if ($tab_count > 0) {
                        $guidance_tabs = array();

                        for ($current_tab = 1; $current_tab <= $tab_count; $current_tab++) {
                            $new_tab = array();
                            $section_count = get_post_meta($page->ID, '_content_tabs-tab-' . $current_tab . '-section-count', true);
                            $new_tab['sections'] = array();
                            $new_tab['tab_title'] = get_post_meta($page->ID, '_content_tabs-tab-' . $current_tab . '-title', true);

                            for ($current_section = 1; $current_section <= $section_count; $current_section++) {
                                $section_title = get_post_meta($page->ID, '_content_tabs-tab-' . $current_tab . '-section-' . $current_section . '-title', true);
                                $section_content = get_post_meta($page->ID, '_content_tabs-tab-' . $current_tab . '-section-' . $current_section . '-content', true);
                                $new_tab['sections'][] = array(
                                    'section_title' => $section_title,
                                    'section_content' => $section_content,
                                    'section_html_content' => ''
                                );
                            }

                            $new_tab['links'] = array();

                            if (is_array($tab_links[$current_tab - 1]) && count($tab_links[$current_tab - 1]) > 0) {
                                $new_tab['links'] = $tab_links[$current_tab - 1];
                            }

                            $guidance_tabs[] = $new_tab;
                        }

                        update_field('field_572320a8bc14c', $guidance_tabs, $page->ID);
                        echo '<li>Assigned tabs for page: "' . $page->post_title . '" (' . $page->ID . ')</li>';

                    } else {
                        echo '<li>Skipped Page (No Tabs Found): "' . $page->post_title . '" (' . $page->ID . ')</li>';
                    }
                }
                else {
                    echo '<li>Skipped Page (Tabs already defined): "' . $page->post_title . '" (' . $page->ID . ')</li>';
                }
            }
        }
        echo '</ul>';
    }
}
