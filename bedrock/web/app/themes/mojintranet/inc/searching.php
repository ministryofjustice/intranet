<?php
// -------------------------------------------------
// Functions to enhance searching (using Relevanssi)
// -------------------------------------------------
/**
 * Adds the Guidance Tabs custom fields to the search index
 * Filter: relevanssi_index_custom_fields
 *
 * @param array $cf -  custom fields to index in search
 * @return array
 */
function dw_index_tab_fields($cf) {
    global $post;
    $tab_num = get_post_meta($post->ID, 'guidance_tabs', true);
    if (is_numeric($tab_num)) {
        for ($t = 0; $t < $tab_num; $t++) {
            $cf[] = 'guidance_tabs_' . $t . '_tab_title';

            $section_num = get_post_meta($post->ID, 'guidance_tabs_'.$t.'_sections', true);

            if (is_numeric($section_num)) {
                for ($s = 0; $s < $section_num; $s++) {
                    $cf[] = 'guidance_tabs_' . $t . '_sections_' . $s . '_section_title';
                    $cf[] = 'guidance_tabs_' . $t . '_sections_' . $s . '_section_html_content';
                }
            }

            $links_num = get_post_meta($post->ID, 'guidance_tabs_'.$t.'_links', true);

            if (is_numeric($links_num)) {
                for ($l = 0; $l < $links_num; $l++) {
                    $cf[] = 'guidance_tabs_' . $t . '_links_' . $l . '_link_title';
                }
            }
        }
    }

    return $cf;
}
add_filter('relevanssi_index_custom_fields', 'dw_index_tab_fields');

function custom_relevanssi_excerpts($content, $post, $query) {

    $tab_content = get_post_meta($post->ID, 'guidance_tabs_0_sections_0_section_html_content', true);

    if ($tab_content != false) {
        $content = $tab_content;
    }

    return $content;
  }

add_filter('relevanssi_excerpt_content', 'custom_relevanssi_excerpts', 10, 3);

// Enable the Relevanssi premium search stemmer
$gis = "general_intranet_enable_search_stemmer";
$stemmer = get_option($gis);
if ($stemmer) {
  add_filter('relevanssi_stemmer', 'relevanssi_simple_english_stemmer');
}

// Pair of functions to remove ampersands from the search index so G&S==GS
add_filter('relevanssi_remove_punctuation', 'saveampersands_1', 9);
function saveampersands_1($a) {
    $a = str_replace('&', 'AMPERSAND', $a);
    return $a;
}
add_filter('relevanssi_remove_punctuation', 'saveampersands_2', 11);
function saveampersands_2($a) {
    $a = str_replace('AMPERSAND', '&', $a);
    return $a;
}
