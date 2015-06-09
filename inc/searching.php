<?php
// -------------------------------------------------
// Functions to enhance searching (using Relevanssi)
// -------------------------------------------------

/**
 * Combines custom fields to create one field for Relevanssi to index/search
 * @param string $meta_key The name the new field should be given
 * @param  array $content The combined content
 * @param int $post_id ID of the post the meta data is to be attached to
 * @return [type]         [description]
 */
function create_search_content($meta_key,$content,$post_id) {
  // Ensures that '_' (underscore) is present at beginning of $field_name
  if(substr($meta_key, 0, 1)!="_") {
    $meta_key = "_$meta_key";
  }

  $meta_id = update_post_meta( $post_id, $meta_key, $content );

  return $meta_id;
}

/**
 * Filters Relevanssi excerpts
 */
function custom_relevanssi_excerpts($content, $post, $query) {
  // Adds custom fields to Relevanssi
  $custom_field = get_post_meta($post->ID, '_tabs_search', true);
  $content .= " " . $custom_field;
  $custom_field = get_post_meta($post->ID, '_quicklinks_search', true);
  $content .= " " . $custom_field;
  // Remove phrases from excerpt
  $unwanted_phrases = array(
    "Tab 1",
    "Tab 2",
    "Tab 3",
    "Tab 4",
    "Tab 5",
    "Tab 6"
    );
  $content = str_replace($unwanted_phrases, "", $content);
  return $content;
}
add_filter('relevanssi_excerpt_content', 'custom_relevanssi_excerpts', 10, 3);

// Elevate exact title matches to top of search results
function exact_title_matches($match) {
  global $wp_query;

  // Get search query and convert to lower case
  $search_query = urldecode(strtolower($wp_query->query['param3']));
  if ($search_query == strtolower(get_the_title($match->doc))) {
    $match->weight = $match->weight * 10;
  }
  return $match;
}
add_filter('relevanssi_match', 'exact_title_matches');

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