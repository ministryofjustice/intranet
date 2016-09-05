<?php
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
