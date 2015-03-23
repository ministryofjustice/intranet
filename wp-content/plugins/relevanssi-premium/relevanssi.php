<?php
/*
Plugin Name: Relevanssi Premium
Plugin URI: http://www.relevanssi.com/
Description: This premium plugin replaces WordPress search with a relevance-sorting search.
Version: 1.13.3
Author: Mikko Saari
Author URI: http://www.mikkosaari.fi/
*/

/*  Copyright 2015 Mikko Saari  (email: mikko@mikkosaari.fi)

    This file is part of Relevanssi Premium, a search plugin for WordPress.

    Relevanssi Premium is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Relevanssi Premium is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Relevanssi Premium.  If not, see <http://www.gnu.org/licenses/>.
*/

// For debugging purposes
// error_reporting(E_ALL);
// ini_set("display_errors", 1); 
// define('WP-DEBUG', true);

add_action('init', 'relevanssi_premium_init');
add_action('init', 'relevanssi_wptuts_activate_au');
add_action('profile_update', 'relevanssi_profile_update');
add_action('edit_user_profile_update', 'relevanssi_profile_update');
add_action('user_register', 'relevanssi_profile_update');
add_action('delete_user', 'relevanssi_delete_user');
add_action('edit_term', 'relevanssi_edit_term');
add_action('delete_term', 'relevanssi_delete_taxonomy_term');
add_action('wpmu_new_blog', 'relevanssi_new_blog', 10, 6); 		
add_action('save_post', 'relevanssi_save_postdata');
add_filter('the_permalink', 'relevanssi_permalink');
add_filter('relevanssi_permalink', 'relevanssi_permalink');
add_filter('wpmu_drop_tables', 'relevanssi_wpmu_drop');

global $wpdb;
global $relevanssi_variables;

$relevanssi_variables['relevanssi_table'] = $wpdb->prefix . "relevanssi";
$relevanssi_variables['stopword_table'] = $wpdb->prefix . "relevanssi_stopwords";
$relevanssi_variables['log_table'] = $wpdb->prefix . "relevanssi_log";
$relevanssi_variables['relevanssi_cache'] = $wpdb->prefix . "relevanssi_cache";
$relevanssi_variables['relevanssi_excerpt_cache'] = $wpdb->prefix . "relevanssi_excerpt_cache";
$relevanssi_variables['post_type_weight_defaults']['post_tag'] = 0.5;
$relevanssi_variables['post_type_weight_defaults']['category'] = 0.5;
$relevanssi_variables['title_boost_default'] = 5;
$relevanssi_variables['link_boost_default'] = 0.75;
$relevanssi_variables['comment_boost_default'] = 0.75;
$relevanssi_variables['database_version'] = 17;
$relevanssi_variables['plugin_version'] = "1.13.3";
$relevanssi_variables['plugin_dir'] = plugin_dir_path(__FILE__);
$relevanssi_variables['plugin_basename'] = plugin_basename(__FILE__);
$relevanssi_variables['file'] = __FILE__;

define('RELEVANSSI_PREMIUM', true);

require_once('lib/init.php');
require_once('lib/interface.php');
require_once('lib/indexing.php');
require_once('lib/stopwords.php');
require_once('lib/search.php');
require_once('lib/excerpts-highlights.php');
require_once('lib/shortcodes.php');
require_once('lib/common.php');
require_once('lib/autoupdate.php');
require_once('lib/SpellCorrector.php');

function relevanssi_premium_init() {
	if (get_option('relevanssi_hide_post_controls') == 'off') {
		add_action('add_meta_boxes', 'relevanssi_add_metaboxes');
	}

	if (get_option('relevanssi_index_synonyms') == 'on') {
		add_filter('relevanssi_post_to_index', 'relevanssi_index_synonyms', 10);
	}
	
	return;
}

function relevanssi_check_old_data() {
	global $wpdb, $relevanssi_variables;
	// Clean out empty _relevanssi_hide_post meta fields
	$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '_relevanssi_hide_post' AND meta_value = ''");
	
	// Version 1.13 changed taxonomy term indexing
	$taxonomies = $wpdb->get_var("SELECT doc FROM " . $relevanssi_variables['relevanssi_table'] . " WHERE type = 'taxonomy'");
	if ($taxonomies != NULL) {
		function relevanssi_taxonomies_warning() {
			echo "<div id='relevanssi-warning' class='error'><p><strong>"
			   . sprintf(__('Thanks for updating the plugin. After the update, Relevanssi requires re-indexing in order to handle taxonomy terms better. You can reindex at <a href="%1$s">the
			   settings page</a>. If you just completed indexing, disregard this message - all is good and this message should not appear again. Thanks!'), "options-general.php?page=relevanssi-premium/relevanssi.php")
			   . "</strong></p></div>";
		}
		add_action('admin_notices', 'relevanssi_taxonomies_warning');
	}

	// Version 1.12 removes the cache feature
	$cache = get_option('relevanssi_enable_cache', 'nothing');
	if ($cache != 'nothing') {
		$relevanssi_cache = $wpdb->prefix . "relevanssi_cache";
		$relevanssi_excerpt_cache = $wpdb->prefix . "relevanssi_excerpt_cache";

		$wpdb->query("DROP TABLE $relevanssi_cache");
		$wpdb->query("DROP TABLE $relevanssi_excerpt_cache");

		delete_option('relevanssi_enable_cache');
		delete_option('relevanssi_cache_seconds');
		wp_clear_scheduled_hook('relevanssi_truncate_cache');
	}	

	// Version 1.10.5 combined taxonomy indexing options
	$inctags = get_option('relevanssi_include_tags', 'nothing');
	if ($inctags == 'on') {
		$taxonomies = get_option('relevanssi_index_taxonomies_list');
		if (!is_array($taxonomies)) $taxonomies = array();
		$taxonomies[] = 'post_tag';
		update_option('relevanssi_index_taxonomies_list', $taxonomies);
		delete_option('relevanssi_include_tags');
	}
	$inccats = get_option('relevanssi_include_cats', 'nothing');
	if ($inccats == 'on') {
		$taxonomies = get_option('relevanssi_index_taxonomies_list');
		if (!is_array($taxonomies)) $taxonomies = array();
		$taxonomies[] = 'category';
		update_option('relevanssi_index_taxonomies_list', $taxonomies);
		delete_option('relevanssi_include_cats');
	}
	$custom = get_option('relevanssi_custom_taxonomies', 'nothing');
	if ($custom != 'nothing') {
		$cts = explode(",", $custom);
		$taxonomies = get_option('relevanssi_index_taxonomies_list');
		if (!is_array($taxonomies)) $taxonomies = array();
		foreach ($cts as $taxonomy) {
			$taxonomy = trim($taxonomy);
			$taxonomies[] = $taxonomy;
		}
		update_option('relevanssi_index_taxonomies_list', $taxonomies);
		delete_option('relevanssi_custom_taxonomies');
	}
	$taxos = get_option('relevanssi_taxonomies_to_index', 'nothing');
	if ($taxos != 'nothing') {
		if ($taxos == 'all') {
			$taxonomies = get_option('relevanssi_index_terms');
			if (!is_array($taxonomies)) $taxonomies = array();
			$all_taxonomies = get_taxonomies('', 'names');
			foreach ($all_taxonomies as $taxonomy) {
				$taxonomies[] = $taxonomy;
			}
			update_option('relevanssi_index_terms', $taxonomies);
			delete_option('relevanssi_taxonomies_to_index');
		}
		else {
			$cts = explode(",", $taxos);
			$taxonomies = get_option('relevanssi_index_terms');
			if (!is_array($taxonomies)) $taxonomies = array();
			foreach ($cts as $taxonomy) {
				$taxonomy = trim($taxonomy);
				$taxonomies[] = $taxonomy;
			}
			update_option('relevanssi_index_terms', $taxonomies);
			delete_option('relevanssi_taxonomies_to_index');
		}
	}
	
	$limit = get_option('relevanssi_throttle_limit');
	if (empty($limit)) update_option('relevanssi_throttle_limit', 500);

	if ($relevanssi_variables['database_version'] == 15) {
		$res = $wpdb->query("SHOW INDEX FROM " . $relevanssi_variables['relevanssi_table'] . " WHERE Key_name = 'typeitem'");
		if ($res == 0) $wpdb->query("ALTER TABLE " . $relevanssi_variables['relevanssi_table'] . " ADD INDEX `typeitem` (`type`, `item`)");
	}

	// Version 1.7.3 renamed relevanssi_hide_post
	/*
	$query = "UPDATE $wpdb->postmeta SET meta_key = '_relevanssi_hide_post' WHERE meta_key = 'relevanssi_hide_post'";
	$wpdb->query($query);
	*/
	
	// Version 1.6.3 removed relevanssi_tag_boost
	$tag_boost = get_option('relevanssi_tag_boost', 'nothing');
	if ($tag_boost != 'nothing') {
		$post_type_weights = get_option('relevanssi_post_type_weights');
		if (!is_array($post_type_weights)) {
			$post_type_weights = array();
		}
		$post_type_weights['post_tag'] = $tag_boost;
		delete_option('relevanssi_tag_boost');
		update_option('relevanssi_post_type_weights', $post_type_weights);
	}

	$index_type = get_option('relevanssi_index_type', 'nothing');
	if ($index_type != 'nothing') {
		// Delete unused options from 1.5 versions
		$post_types = get_option('relevanssi_index_post_types');
		
		if (!is_array($post_types)) $post_types = array();
		
		switch ($index_type) {
			case "posts":
				array_push($post_types, 'post');
				break;
			case "pages":
				array_push($post_types, 'page');
				break;
			case 'public':
				if (function_exists('get_post_types')) {
					$pt_1 = get_post_types(array('exclude_from_search' => '0'));
					$pt_2 = get_post_types(array('exclude_from_search' => false));
					foreach (array_merge($pt_1, $pt_2) as $type) {
						array_push($post_types, $type);				
					}
				}
				break;
			case "both": 								// really should be "everything"
				$pt = get_post_types();
				foreach ($pt as $type) {
					array_push($post_types, $type);				
				}
				break;
		}
		
		$attachments = get_option('relevanssi_index_attachments');
		if ('on' == $attachments) array_push($post_types, 'attachment');
		
		$custom_types = get_option('relevanssi_custom_types');
		$custom_types = explode(',', $custom_types);
		if (is_array($custom_types)) {
			foreach ($custom_types as $type) {
				$type = trim($type);
				if (substr($type, 0, 1) != '-') {
					array_push($post_types, $type);
				}
			}
		}
		
		update_option('relevanssi_index_post_types', $post_types);
		
		delete_option('relevanssi_index_type');
		delete_option('relevanssi_index_attachments');
		delete_option('relevanssi_custom_types');
	}
}

function relevanssi_didyoumean($query, $pre, $post, $n = 5) {
	global $wpdb, $relevanssi_variables, $wp_query;
	
	$total_results = $wp_query->found_posts;	
	
	if ($total_results > $n) return;

	$suggestion = "";
	$suggestion_enc = "";
	$exact_match = false;

	if (class_exists('SpellCorrector')) {
		$tokens = relevanssi_tokenize($query);

		$sc = new SpellCorrector();

		$correct = array();
		foreach ($tokens as $token => $count) {
			$token = trim($token);
			$c = $sc->correct($token);
			if ($c !== strval($token)) {
				array_push($correct, $c);
			}
			else {
				$exact_match = true;
			}
		}
		if (count($correct) > 0) {
			$suggestion = implode(' ', $correct);
			$suggestion_enc = urlencode($suggestion);
		}
	}

	if ("" == $suggestion && !$exact_match) {
		$q = "SELECT query, count(query) as c, AVG(hits) as a FROM " . $relevanssi_variables['log_table'] . " WHERE hits > 1 GROUP BY query ORDER BY count(query) DESC";
		$q = apply_filters('relevanssi_didyoumean_query', $q);
		
		$data = $wpdb->get_results($q);
				
		$distance = -1;
		$closest = "";
			
		foreach ($data as $row) {
			if ($row->c < 2) break;
			$lev = levenshtein($query, $row->query);
			
			if ($lev < $distance || $distance < 0) {
				if ($row->a > 0) {
					$distance = $lev;
					$closest = $row->query;
					if ($lev == 1) break; // get the first with distance of 1 and go
				}
			}
		}
			
		if ($distance > 0) {
			$suggestion = $closest;
			$suggestion_enc = urlencode($closest);
		}
	}
	
	if ($suggestion) {
 		$url = get_bloginfo('url');
		$url = esc_attr(add_query_arg(array(
			's' => $suggestion_enc
			), $url));
		$url = apply_filters('relevanssi_didyoumean_url', $url);
		echo "$pre<a href='$url'>$suggestion</a>$post";
 	}
}

function relevanssi_profile_update($user) {
	if (get_option('relevanssi_index_users') == 'on') {
		$update = true;
		relevanssi_index_user($user, $update);
	}
}

function relevanssi_edit_term($term) {
	if (get_option('relevanssi_index_taxonomies') == 'on') {	
		$update = true;
		global $wpdb;
		$taxonomy = $wpdb->get_var("SELECT taxonomy FROM $wpdb->term_taxonomy WHERE term_id=$term");
		relevanssi_index_taxonomy_term($term, $taxonomy, $update);
	}
}

function relevanssi_delete_user($user) {
	global $wpdb, $relevanssi_variables;
	$wpdb->query("DELETE FROM " . $relevanssi_variables['relevanssi_table'] . " WHERE item = $user AND type = 'user'");
}

function relevanssi_delete_taxonomy_term($term) {
	global $wpdb, $relevanssi_variables;
	$wpdb->query("DELETE FROM " . $relevanssi_variables['relevanssi_table'] . " WHERE item = $term AND type = 'taxonomy'");
}

function relevanssi_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	global $wpdb;
 
	if (is_plugin_active_for_network('relevanssi-premium/relevanssi.php')) {
		switch_to_blog($blog_id);
		_relevanssi_install();
		restore_current_blog();
	}
}

function relevanssi_install($network_wide = false) {
	global $wpdb;

	if ($network_wide) {
		$blogids = $wpdb->get_col($wpdb->prepare("
			SELECT blog_id
			FROM $wpdb->blogs
			WHERE site_id = %d
			AND deleted = 0
			AND spam = 0
		", $wpdb->siteid));

		foreach ($blogids as $blog_id) {
			switch_to_blog($blog_id);
			_relevanssi_install();
		}

		restore_current_blog();
	} else {
		_relevanssi_install();
	}
}

function _relevanssi_install() {
	global $relevanssi_variables;
	
	add_option('relevanssi_title_boost', $relevanssi_variables['title_boost_default']);
	add_option('relevanssi_link_boost', $relevanssi_variables['link_boost_default']);
	add_option('relevanssi_comment_boost', $relevanssi_variables['comment_boost_default']);
	add_option('relevanssi_admin_search', 'off');
	add_option('relevanssi_highlight', 'strong');
	add_option('relevanssi_txt_col', '#ff0000');
	add_option('relevanssi_bg_col', '#ffaf75');
	add_option('relevanssi_css', 'text-decoration: underline; text-color: #ff0000');
	add_option('relevanssi_class', 'relevanssi-query-term');
	add_option('relevanssi_excerpts', 'on');
	add_option('relevanssi_excerpt_length', '30');
	add_option('relevanssi_excerpt_type', 'words');
	add_option('relevanssi_excerpt_allowable_tags', '');
	add_option('relevanssi_log_queries', 'off');
	add_option('relevanssi_log_queries_with_ip', 'off');
	add_option('relevanssi_cat', '0');
	add_option('relevanssi_excat', '0');
	add_option('relevanssi_extag', '0');
	add_option('relevanssi_index_fields', '');
	add_option('relevanssi_exclude_posts', ''); 		//added by OdditY
	add_option('relevanssi_hilite_title', ''); 			//added by OdditY	
	add_option('relevanssi_highlight_docs', 'off');
	add_option('relevanssi_highlight_docs_external', 'off');
	add_option('relevanssi_highlight_comments', 'off');
	add_option('relevanssi_index_comments', 'none');	//added by OdditY
	add_option('relevanssi_show_matches', '');
	add_option('relevanssi_show_matches_text', '(Search hits: %body% in body, %title% in title, %tags% in tags, %comments% in comments. Score: %score%)');
	add_option('relevanssi_fuzzy', 'sometimes');
	add_option('relevanssi_indexed', '');
	add_option('relevanssi_expand_shortcodes', 'on');
	add_option('relevanssi_index_author', '');
	add_option('relevanssi_implicit_operator', 'OR');
	add_option('relevanssi_omit_from_logs', '');
	add_option('relevanssi_synonyms', '');
	add_option('relevanssi_index_excerpt', 'off');
	add_option('relevanssi_index_limit', '500');
	add_option('relevanssi_disable_or_fallback', 'off');
	add_option('relevanssi_respect_exclude', 'on');
	add_option('relevanssi_min_word_length', '3');
	add_option('relevanssi_throttle', 'on');
	add_option('relevanssi_throttle_limit', '500');
	add_option('relevanssi_db_version', '1');
	add_option('relevanssi_wpml_only_current', 'on');
	add_option('relevanssi_post_type_weights', '');
	add_option('relevanssi_index_users', 'off');
	add_option('relevanssi_index_subscribers', 'off');
	add_option('relevanssi_index_taxonomies', 'off');
	add_option('relevanssi_internal_links', 'noindex');
	add_option('relevanssi_word_boundaries', 'on');
	add_option('relevanssi_default_orderby', 'relevance');
	add_option('relevanssi_thousand_separator', '');
	add_option('relevanssi_disable_shortcodes', '');
	add_option('relevanssi_api_key', '');
	add_option('relevanssi_index_post_types', array('post', 'page'));
	add_option('relenvassi_recency_bonus', array('bonus' => '', 'days' => ''));
	add_option('relevanssi_mysql_columns', '');
	add_option('relevanssi_hide_post_controls', 'off');
	add_option('relevanssi_index_taxonomies_list', array());
	add_option('relevanssi_index_terms', array());
	add_option('relevanssi_index_synonyms', 'off');

	relevanssi_create_database_tables($relevanssi_variables['database_version']);
}

add_filter('query_vars', 'relevanssi_premium_query_vars');
function relevanssi_premium_query_vars($qv) {
	$qv[] = 'searchblogs';
	$qv[] = 'customfield_key';
	$qv[] = 'customfield_value';
	$qv[] = 'operator';
	return $qv;
}

function relevanssi_set_operator($query) {
	isset($query->query_vars['operator']) ?
		$operator = $query->query_vars['operator'] : 
		$operator = get_option("relevanssi_implicit_operator");
	return $operator;
}

function relevanssi_process_taxonomies($taxonomy, $taxonomy_term, $tax_query) {
	$taxonomies = explode('|', $taxonomy);
	$terms = explode('|', $taxonomy_term);
	$i = 0;
	foreach ($taxonomies as $taxonomy) {
		$term_tax_id = null;
		$taxonomy_terms = explode(',', $terms[$i]);
		foreach ($taxonomy_terms as $taxonomy_term) {
			if (!empty($taxonomy_term))
				$tax_query[] = array('taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $taxonomy_term);
		}
		$i++;
	}
	return $tax_query;
}

/* 	Custom-made get_posts() replacement that creates post objects for
	users and taxonomy terms. For regular posts, the function uses
	a caching mechanism.
*/
function relevanssi_get_post($id) {
	global $relevanssi_post_array;
	
	$type = substr($id, 0, 2);	
	switch ($type) {
		case 'u_':
			list($throwaway, $id) = explode('_', $id);
			$user = get_userdata($id);
		
			$post = new stdClass;
			$post->post_title = $user->display_name;
			$post->post_content = $user->description;
			$post->post_type = 'user';
			$post->ID = $id;
			$post->link = get_author_posts_url($id);
			$post->post_status = 'publish';
			$post->post_date = date("Y-m-d H:i:s");
			$post->post_author = 0;
			$post->post_name = '';
			$post->post_excerpt = '';
			$post->comment_status = '';
			$post->ping_status = '';
			$post->user_id = $id;
		
			$post = apply_filters('relevanssi_user_profile_to_post', $post);
			break;
		case '**':
			list($throwaway, $taxonomy, $id) = explode('**', $id);
			$term = get_term($id, $taxonomy);

			$post = new stdClass;
			$post->post_title = $term->name;
			$post->post_content = $term->description;
			$post->post_type = $taxonomy;
			$post->ID = -1;
			$post->post_status = 'publish';
			$post->post_date = date("Y-m-d H:i:s");
			$post->link = get_term_link($term, $taxonomy);
			$post->post_author = 0;
			$post->post_name = '';
			$post->post_excerpt = '';
			$post->comment_status = '';
			$post->ping_status = '';
			$post->term_id = $id;
			
			$post = apply_filters('relevanssi_taxonomy_term_to_post', $post);
			break;
		default:
			if (isset($relevanssi_post_array[$id])) {
				$post = $relevanssi_post_array[$id];
			}
			else {
				$post = get_post($id);
			}
	}
	return $post;
}

function relevanssi_permalink($content, $link_post = NULL) {
	if ($link_post == NULL) {
		global $post;
		if (isset($post->link))
			$content = $post->link;
	}
	return $content;
}

function relevanssi_get_permalink() {
	$permalink = apply_filters('relevanssi_permalink', get_permalink());
	return $permalink;
}

function relevanssi_correct_query($q) {
	if (class_exists('SpellCorrector')) {
		$tokens = relevanssi_tokenize($q, false);
		$sc = new SpellCorrector();
		$correct = array();
		foreach ($tokens as $token => $count) {
			$c = $sc->correct($token);
			if ($c !== $token) array_push($correct, $c);
		}
		if (count($correct) > 0) $q = implode(' ', $correct);
	}
	return $q;
}

function relevanssi_search_multi($q, $search_blogs = NULL, $post_type) {
	global $relevanssi_variables, $wpdb;

	$values_to_filter = array(
		'q' => $q,
		'post_type' => $post_type,
		'search_blogs' => $search_blogs,
		);
	$filtered_values = apply_filters( 'relevanssi_search_filters', $values_to_filter );
	$q               = $filtered_values['q'];
	$post_type       = $filtered_values['post_type'];
	$search_blogs    = $filtered_values['search_blogs'];

	$hits = array();
	
	$remove_stopwords = false;
	$terms = relevanssi_tokenize($q, $remove_stopwords);
	
	if (count($terms) < 1) {
		// Tokenizer killed all the search terms.
		return $hits;
	}
	$terms = array_keys($terms); // don't care about tf in query

	$total_hits = 0;
		
	$title_matches = array();
	$tag_matches = array();
	$link_matches = array();
	$comment_matches = array();
	$body_matches = array();
	$scores = array();
	$term_hits = array();
	$hitsbyweight = array();

	$operator = get_option('relevanssi_implicit_operator');
	$fuzzy = get_option('relevanssi_fuzzy');

	$query_restrictions = "";
	if ($post_type) {
		if ($post_type == -1) $post_type = null; // Facetious sets post_type to -1 if not selected
		if (!is_array($post_type)) {
			$post_types = esc_sql(explode(',', $post_type));
		}
		else {
			$post_types = esc_sql($post_type);
		}
		$post_type = count($post_types) ? "'" . implode( "', '", $post_types) . "'" : 'NULL';
		$query_restrictions .= " AND doc IN (SELECT DISTINCT(ID) FROM $wpdb->posts
			WHERE post_type IN ($post_type))";
	}
	$query_restrictions = apply_filters('relevanssi_where', $query_restrictions); // Charles St-Pierre

	$search_blogs = explode(",", $search_blogs);
	$post_type_weights = get_option('relevanssi_post_type_weights');
	
	$orig_blog = $wpdb->blogid;
	foreach ($search_blogs as $blogid) {
		switch_to_blog($blogid);
		$relevanssi_table = $wpdb->prefix . "relevanssi";

		$D = $wpdb->get_var("SELECT COUNT(DISTINCT(doc)) FROM $relevanssi_table");
	
		$no_matches = true;
		if ("always" == $fuzzy) {
			$o_term_cond = "(term LIKE '%#term#' OR term LIKE '#term#%') ";
		}
		else {
			$o_term_cond = " term = '#term#' ";
		}
			
		$min_length = get_option('relevanssi_min_word_length');
		$search_again = false;
		do {
			foreach ($terms as $term) {
				$term = trim($term);	// numeric search terms will start with a space
				if (strlen($term) < $min_length) continue;
				if (method_exists($wpdb, 'esc_like')) {
					$term = $wpdb->esc_like(esc_sql($term));
				}
				else {
					// Compatibility for pre-4.0 WordPress
					$term = like_escape(esc_sql($term));
				}
				$term_cond = str_replace('#term#', $term, $o_term_cond);		
	
				$query = "SELECT *, title + content + comment + tag + link + author + category + excerpt + taxonomy + customfield AS tf 
				FROM $relevanssi_table WHERE $term_cond $query_restrictions";
				$query = apply_filters('relevanssi_query_filter', $query);
				// Clean: $term is escaped, as are $query_restrictions

				$matches = $wpdb->get_results($query);
				if (count($matches) < 1) {
					continue;
				}
				else {
					$no_matches = false;
				}
			
				$total_hits += count($matches);
	
				$query = "SELECT COUNT(DISTINCT(doc)) FROM $relevanssi_table WHERE $term_cond $query_restrictions";
				$query = apply_filters('relevanssi_df_query_filter', $query);
	
				$df = $wpdb->get_var($query);
				// Clean: $term is escaped, as are $query_restrictions
	
				if ($df < 1 && "sometimes" == $fuzzy) {
					$query = "SELECT COUNT(DISTINCT(doc)) FROM $relevanssi_table
						WHERE (term LIKE '%$term' OR term LIKE '$term%') $query_restrictions";
					$query = apply_filters('relevanssi_df_query_filter', $query);
					$df = $wpdb->get_var($query);
					// Clean: $term is escaped, as are $query_restrictions
				}
			
				$title_boost = floatval(get_option('relevanssi_title_boost'));
				isset($post_type_weights['post_tag']) ? $tag_boost = $post_type_weights['post_tag'] : 1;
				$link_boost = floatval(get_option('relevanssi_link_boost'));
				$comment_boost = floatval(get_option('relevanssi_comment_boost'));
			
				$doc_weight = array();
				$scores = array();
				$term_hits = array();
			
				$idf = log($D / (1 + $df));
				foreach ($matches as $match) {
					$match->tf =
						$match->title * $title_boost +
						$match->content +
						$match->comment * $comment_boost +
						$match->tag * $tag_boost +
						$match->link * $link_boost +
						$match->author +
						$match->category +
						$match->excerpt +
						$match->taxonomy +
						$match->customfield;

					$term_hits[$match->doc][$term] =
						$match->title +
						$match->content +
						$match->comment +
						$match->tag +
						$match->link +
						$match->author +
						$match->category +
						$match->excerpt +
						$match->taxonomy +
						$match->customfield;

					$match->weight = $match->tf * $idf;
	
					$match = apply_filters('relevanssi_match', $match);

					$doc_terms[$match->doc][$term] = true; // count how many terms are matched to a doc
					isset($doc_weight[$match->doc]) ?
						$doc_weight[$match->doc] += $match->weight :
						$doc_weight[$match->doc] = $match->weight;
					isset($scores[$match->doc]) ?
						$scores[$match->doc] += $match->weight :
						$scores[$match->doc] = $match->weight;

					$body_matches[$match->doc] = $match->content;
					$title_matches[$match->doc] = $match->title;
					$link_matches[$match->doc] = $match->link;
					$tag_matches[$match->doc] = $match->tag;
					$comment_matches[$match->doc] = $match->comment;
				}
			}

			if ($no_matches) {
				if ($search_again) {
					// no hits even with fuzzy search!
					$search_again = false;
				}
				else {
					if ("sometimes" == $fuzzy) {
						$search_again = true;
						$o_term_cond = "(term LIKE '%#term#' OR term LIKE '#term#%') ";
					}
				}
			}
			else {
				$search_again = false;
			}
		} while ($search_again);

		$strip_stops = true;
		$terms_without_stops = array_keys(relevanssi_tokenize(implode(' ', $terms), $strip_stops));
		$total_terms = count($terms_without_stops);
	
		if (isset($doc_weight) && count($doc_weight) > 0 && !$no_matches) {
			arsort($doc_weight);
			$i = 0;
			foreach ($doc_weight as $doc => $weight) {
				if (count($doc_terms[$doc]) < $total_terms && $operator == "AND") {
					// AND operator in action:
					// doc didn't match all terms, so it's discarded
					continue;
				}
				$status = get_post_status($doc);
				$post_ok = true;
				if ('private' == $status) {
					$post_ok = false;
	
					if (function_exists('awp_user_can')) {
						// Role-Scoper
						$current_user = wp_get_current_user();
						$post_ok = awp_user_can('read_post', $doc, $current_user->ID);
					}
					else {
						// Basic WordPress version
						$type = get_post_type($doc);
						$cap = 'read_private_' . $type . 's';
						if (current_user_can($cap)) {
							$post_ok = true;
						}
					}
				} else if ( 'publish' != $status ) {
					$post_ok = false;
				}
				if ($post_ok) {
					$post_object = get_blog_post($blogid, $doc);
					$post_object->blog_id = $blogid;

					$object_id = $blogid . '|' . $doc;
					$hitsbyweight[$object_id] = $weight;
					$post_objects[$object_id] = $post_object;
				}
			}
		}
	}
	switch_to_blog($orig_blog);
	
	arsort($hitsbyweight);
	$i = 0;
	foreach ($hitsbyweight as $hit => $weight) {
		$hit = $post_objects[$hit];
		$hits[intval($i++)] = $hit;
	}

	global $wp;	
	$default_order = get_option('relevanssi_default_orderby', 'relevance');
	isset($wp->query_vars["orderby"]) ? $orderby = $wp->query_vars["orderby"] : $orderby = $default_order;
	isset($wp->query_vars["order"]) ? $order = $wp->query_vars["order"] : $order = 'desc';
	if ($orderby != 'relevance')
		relevanssi_object_sort($hits, $orderby, $order);

	$return = array('hits' => $hits, 'body_matches' => $body_matches, 'title_matches' => $title_matches,
		'tag_matches' => $tag_matches, 'comment_matches' => $comment_matches, 'scores' => $scores,
		'term_hits' => $term_hits, 'query' => $q, 'link_matches' => $link_matches);

	return $return;
}

function relevanssi_recognize_negatives($q) {
	$term = strtok($q, " ");
	$negative_terms = array();
	while ($term !== false) {
		if (substr($term, 0, 1) == '-') array_push($negative_terms, substr($term, 1));
		$term = strtok(" ");
	}
	return $negative_terms;
}

function relevanssi_recognize_positives($q) {
	$term = strtok($q, " ");
	$positive_terms = array();
	while ($term !== false) {
		if (substr($term, 0, 1) == '+') array_push($positive_terms, substr($term, 1));
		$term = strtok(" ");
	}
	return $positive_terms;
}

function relevanssi_negatives_positives($negative_terms, $positive_terms, $relevanssi_table) {
	$query_restrictions = "";
	if ($negative_terms) {
		for ($i = 0; $i < sizeof($negative_terms); $i++) {
			$negative_terms[$i] = "'" . esc_sql($negative_terms[$i]) . "'";
		}
		$negatives = implode(',', $negative_terms);
		$query_restrictions .= " AND doc NOT IN (SELECT DISTINCT(doc) FROM $relevanssi_table WHERE term IN ($negatives))";
		// Clean: escaped
	}
	
	if ($positive_terms) {
		for ($i = 0; $i < sizeof($positive_terms); $i++) {
			$positive_term = esc_sql($positive_terms[$i]);
			$query_restrictions .= " AND doc IN (SELECT DISTINCT(doc) FROM $relevanssi_table WHERE term = '$positive_term')";
			// Clean: escaped
		}
	}
	return $query_restrictions;
}

function relevanssi_get_recency_bonus() {
	$recency_bonus = get_option('relevanssi_recency_bonus');
	if (empty($recency_bonus['days']) OR empty($recency_bonus['bonus'])) {
		$recency_bonus = false;
		$recency_cutoff_date = false;
	}
	if ($recency_bonus) {
		$recency_cutoff_date = time() - 60 * 60 * 24 * $recency_bonus['days'];
	}
	return array($recency_bonus, $recency_cutoff_date);
}

function relevanssi_get_internal_links($text) {
	$links = array();
    if ( preg_match_all( '@<a[^>]*?href="(' . home_url() . '[^"]*?)"[^>]*?>(.*?)</a>@siu', $text, $m ) ) {
		foreach ( $m[1] as $i => $link ) {
			if ( !isset( $links[$link] ) )
				$links[$link] = '';
			$links[$link] .= ' ' . $m[2][$i];
		}
	}
    if ( preg_match_all( '@<a[^>]*?href="(/[^"]*?)"[^>]*?>(.*?)</a>@siu', $text, $m ) ) {
		foreach ( $m[1] as $i => $link ) {
			if ( !isset( $links[$link] ) )
				$links[$link] = '';
			$links[$link] .= ' ' . $m[2][$i];
		}
	}
	if (count($links) > 0)
		return $links;
	return false;
}

function relevanssi_strip_internal_links($text) {
	$text = preg_replace(
		array(
			'@<a[^>]*?href="' . home_url() . '[^>]*?>.*?</a>@siu',
		),
		' ',
		$text );
	$text = preg_replace(
		array(
			'@<a[^>]*?href="/[^>]*?>.*?</a>@siu',
		),
		' ',
		$text );
	return $text;
}

function relevanssi_nonlocal_highlighting($referrer, $content, $query) {
	if (get_option('relevanssi_highlight_docs_external', 'off') != 'off') {
		$query = relevanssi_add_synonyms($query);
		if (strpos($referrer, 'google') !== false) {
			$content = relevanssi_highlight_terms($content, $query);
		} elseif (strpos($referrer, 'bing') !== false) {
			$content = relevanssi_highlight_terms($content, $query);
		} elseif (strpos($referrer, 'ask') !== false) {
			$content = relevanssi_highlight_terms($content, $query);
		} elseif (strpos($referrer, 'aol') !== false) {
			$content = relevanssi_highlight_terms($content, $query);
		} elseif (strpos($referrer, 'yahoo') !== false) {
			$content = relevanssi_highlight_terms($content, $query);
		}
	}
	return $content;
}

function relevanssi_index_users() {
	global $wpdb, $relevanssi_variables;

	$wpdb->query("DELETE FROM " . $relevanssi_variables['relevanssi_table'] . " WHERE type = 'user'");
	if (function_exists('get_users')) {
		$users_list = get_users();
	}
	else {
		$users_list = get_users_of_blog();
	}

	$users = array();
	foreach ($users_list as $user) {
		$users[] = get_userdata($user->ID);
	}

	$index_subscribers = get_option('relevanssi_index_subscribers');
	foreach ($users as $user) {
		if ($index_subscribers == 'off') {
			$vars = get_object_vars($user);
			$subscriber = false;
			if (is_array($vars["caps"])) {
				foreach ($vars["caps"] as $role => $val) {
					if ($role == 'subscriber') {
						$subscriber = true;
						break;
					}
				}
			}
			if ($subscriber) continue;
		}

		$update = false;
		
		$index_this_user = apply_filters('relevanssi_user_index_ok', true, $user);
		if ($index_this_user) {
			$user = apply_filters('relevanssi_user_add_data', $user);
			relevanssi_index_user($user, $update);
		}
	}
}

function relevanssi_index_user($user, $remove_first = false) {
	global $wpdb, $relevanssi_variables;
	
	if (is_numeric($user)) {
		$user = get_userdata($user);
	}
	
	if ($remove_first)
		relevanssi_delete_user($user->ID);

	$insert_data = array();
	$min_length = get_option('relevanssi_min_word_length', 3);
	
	$user_meta = get_option('relevanssi_index_user_meta');
	if ($user_meta) {
		$user_meta_fields = explode(',', $user_meta);
		foreach ($user_meta_fields as $key) {
			$key = trim($key);
			$values = get_user_meta($user->ID, $key, false);
			foreach($values as $value) {
				$tokens = relevanssi_tokenize($value, true, $min_length); // true = remove stopwords
				foreach($tokens as $term => $tf) {
					isset($insert_data[$term]['content']) ? $insert_data[$term]['content'] += $tf : $insert_data[$term]['content'] = $tf;
				}
			}
		}
	}
	
	$extra_fields = get_option('relevanssi_index_user_fields');
	if ($extra_fields) {
		$extra_fields = explode(',', $extra_fields);
		$user_vars = get_object_vars($user);
		foreach ($extra_fields as $field) {
			$field = trim($field);
			if (isset($user_vars[$field]) || isset($user_vars['data']->$field) || get_user_meta($user->ID, $field, true)) {
				$to_tokenize = "";
				if (isset($user_vars[$field])) {
					$to_tokenize = $user_vars[$field];
				}
				if (empty($to_tokenize) && isset($user_vars['data']->$field)) {
					$to_tokenize = $user_vars['data']->$field;
				}
				if (empty($to_tokenize)) {
					$to_tokenize = get_user_meta($user->ID, $field, true);
				}
				$tokens = relevanssi_tokenize($to_tokenize, true, $min_length); // true = remove stopwords
				foreach($tokens as $term => $tf) {
					isset($insert_data[$term]['content']) ? $insert_data[$term]['content'] += $tf : $insert_data[$term]['content'] = $tf;
				}
			}
		}
	} 
	
	if (isset($user->description) && $user->description != "") {
		$tokens = relevanssi_tokenize($user->description, true, $min_length); // true = remove stopwords
		foreach($tokens as $term => $tf) {
			isset($insert_data[$term]['content']) ? $insert_data[$term]['content'] += $tf : $insert_data[$term]['content'] = $tf;
		}
	}

	if (isset($user->first_name) && $user->first_name != "") {
		$parts = explode(" ", $user->first_name);
		foreach($parts as $part) {
			isset($insert_data[$part]['title']) ? $insert_data[$part]['title']++ : $insert_data[$part]['title'] = 1;
		}
	}

	if (isset($user->last_name) && $user->last_name != "") {
		$parts = explode(" ", $user->last_name);
		foreach($parts as $part) {
			isset($insert_data[$part]['title']) ? $insert_data[$part]['title']++ : $insert_data[$part]['title'] = 1;
		}
	}

	if (isset($user->display_name) && $user->display_name != "") {
		$parts = explode(" ", $user->display_name);
		foreach($parts as $part) {
			isset($insert_data[$part]['title']) ? $insert_data[$part]['title']++ : $insert_data[$part]['title'] = 1;
		}
	}

	$insert_data = apply_filters('relevanssi_user_data_to_index', $insert_data, $user);

	foreach ($insert_data as $term => $data) {
		$content = 0;
		$title = 0;
		$comment = 0;
		$tag = 0;
		$link = 0;
		$author = 0;
		$category = 0;
		$excerpt = 0;
		$taxonomy = 0;
		$customfield = 0;
		extract($data);

		$query = $wpdb->prepare("INSERT IGNORE INTO " . $relevanssi_variables['relevanssi_table'] . "
			(item, doc, term, term_reverse, content, title, comment, tag, link, author, category, excerpt, taxonomy, customfield, type, customfield_detail, taxonomy_detail, mysqlcolumn_detail)
			VALUES (%d, %d, %s, REVERSE(%s), %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %s)",
			$user->ID, -1, $term, $term, $content, $title, $comment, $tag, $link, $author, $category, $excerpt, $taxonomy, $customfield, 'user', '', '', '');
		$wpdb->query($query);
	}
}

function relevanssi_index_taxonomies() {
	global $wpdb, $relevanssi_variables;

	$wpdb->query("DELETE FROM " . $relevanssi_variables['relevanssi_table'] . " WHERE type = 'taxonomy'");
	
	$taxonomies = get_option('relevanssi_index_terms');
	foreach ($taxonomies as $taxonomy) {
		$args = apply_filters('relevanssi_index_taxonomies_args', array());
		$terms = get_terms($taxonomy, $args);
		foreach ($terms as $term) {
			$update = false;
			$term = apply_filters('relevanssi_term_add_data', $term, $taxonomy);
			relevanssi_index_taxonomy_term($term, $taxonomy, $update);
		}
	}
}

function relevanssi_index_taxonomy_term($term, $taxonomy, $remove_first = false) {
	global $wpdb, $relevanssi_variables;
	
	if (is_numeric($term)) {
		$term = get_term($term, $taxonomy);
	}

	if ($remove_first)
		relevanssi_delete_taxonomy_term($term->term_id);

	$insert_data = array();
	
	$min_length = get_option('relevanssi_min_word_length', 3);
	if (isset($term->description) && $term->description != "") {
		$description = apply_filters('relevanssi_tax_term_additional_content', $term->description, $term);
		$tokens = relevanssi_tokenize($description, true, $min_length); // true = remove stopwords
		foreach ($tokens as $t_term => $tf) {
			isset($insert_data[$t_term]['content']) ? $insert_data[$t_term]['content'] += $tf : $insert_data[$t_term]['content'] = $tf;
		}
	}

	if (isset($term->name) && $term->name != "") {
		$tokens = relevanssi_tokenize($term->name, true, $min_length); // true = remove stopwords
		foreach ($tokens as $t_term => $tf) {
			isset($insert_data[$t_term]['title']) ? $insert_data[$t_term]['title'] += $tf : $insert_data[$t_term]['title'] = $tf;
		}
	}

	foreach ($insert_data as $t_term => $data) {
		$t_term = trim($t_term); // Numeric terms start with a space
		$content = 0;
		$title = 0;
		$comment = 0;
		$tag = 0;
		$link = 0;
		$author = 0;
		$category = 0;
		$excerpt = 0;
		$customfield = 0;
		extract($data);

		$query = $wpdb->prepare("INSERT IGNORE INTO " . $relevanssi_variables['relevanssi_table'] . "
			(item, doc, term, term_reverse, content, title, comment, tag, link, author, category, excerpt, taxonomy, customfield, type, customfield_detail, taxonomy_detail, mysqlcolumn_detail)
			VALUES (%d, %d, %s, REVERSE(%s), %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %s)",
			$term->term_id, -1, $t_term, $t_term, $content, $title, $comment, $tag, $link, $author, $category, $excerpt, '', $customfield, $taxonomy, '', '', '');

		$wpdb->query($query);
	}
}

function relevanssi_remove_doc($id, $keep_internal_links = false) {
	global $wpdb, $relevanssi_variables;

	$and = $keep_internal_links ? 'AND link = 0' : '';

	$D = get_option( 'relevanssi_doc_count');

 	$q = "DELETE FROM " . $relevanssi_variables['relevanssi_table'] . " WHERE doc=$id";
	$wpdb->query($q);
	$rows_updated = $wpdb->query($q);

	if($rows_updated && $rows_updated > 0) {
		update_option('relevanssi_doc_count', $D - $rows_updated);
	}
}

function relevanssi_remove_item($id, $type) {
	global $wpdb, $relevanssi_variables;
	
	if ($id == 0 && $type == 'post') {
		return;
		// this should never happen, but in case it does, let's not empty the whole database
	}
	
	$q = "DELETE FROM " . $relevanssi_variables['relevanssi_table'] . " WHERE item = $id AND type = '$type'";
	$wpdb->query($q);
}

function relevanssi_hide_post($id) {
	$hide = get_post_meta($id, '_relevanssi_hide_post', true);
	if ($hide == "on") return true;
	return false;
}

function relevanssi_customfield_detail($insert_data, $token, $count, $field) {
	isset($insert_data[$token]['customfield_detail']) ? $cfdetail = unserialize($insert_data[$token]['customfield_detail']) : $cfdetail = array();
	isset($cfdetail[$field]) ? $cfdetail[$field] += $count : $cfdetail[$field] = $count;
	$insert_data[$token]['customfield_detail'] = serialize($cfdetail);
	return $insert_data;
}

function relevanssi_index_mysql_columns($insert_data, $id) {
	$custom_columns = get_option('relevanssi_mysql_columns');
	if (!empty($custom_columns)) {
		global $wpdb;
		$custom_column_data = $wpdb->get_row("SELECT $custom_columns FROM $wpdb->posts WHERE ID=$id", ARRAY_A);
		if (is_array($custom_column_data)) {
			foreach ($custom_column_data as $data) {
				$data = relevanssi_tokenize($data);
				if (count($data) > 0) {
					foreach ($data as $term => $count) {
						isset($insert_data[$term]['mysqlcolumn']) ? $insert_data[$term]['mysqlcolumn'] += $count : $insert_data[$term]['mysqlcolumn'] = $count;
					}		
				}
			}
		}
	}
	return $insert_data;
}

function relevanssi_process_internal_links($contents, $id) {
	$internal_links_behaviour = get_option('relevanssi_internal_links', 'noindex');

	if ($internal_links_behaviour != 'noindex') {
		global $relevanssi_variables, $wpdb;
		$min_word_length = get_option('relevanssi_min_word_length', 3);
		// index internal links
		$internal_links = relevanssi_get_internal_links($contents);
		if ( !empty( $internal_links ) ) {
	
			foreach ( $internal_links as $link => $text ) {
				$link_id = url_to_postid( $link );
				if ( !empty( $link_id ) ) {
				$link_words = relevanssi_tokenize($text, true, $min_word_length);
					if ( count( $link_words > 0 ) ) {
						foreach ( $link_words as $word => $count ) {
							$wpdb->query("INSERT INTO " . $relevanssi_variables['relevanssi_table'] . " (doc, term, link, item)
							VALUES ($link_id, '$word', $count, $id)");
						}
					}
				}
			}
	
			if ('strip' == $internal_links_behaviour) 
				$contents = relevanssi_strip_internal_links($contents);
		}
	}
	
	return $contents;
}

function relevanssi_thousandsep($str) {
	$thousandsep = get_option('relevanssi_thousand_separator', '');
	if (!empty($thousandsep)) {
		$pattern = "/(\d+)" . $thousandsep . "(\d+)/u";
		$str = preg_replace($pattern, "$1$2", $str);
	}
	return $str;
}

add_filter('relevanssi_premium_tokenizer', 'relevanssi_enable_stemmer');
function relevanssi_enable_stemmer($t) {
	$t = apply_filters('relevanssi_stemmer', $t);
	return $t;
}

function relevanssi_simple_english_stemmer($term) {
	$len = strlen($term);

	$end1 = substr($term, -1, 1);
	if ("s" == $end1 && $len > 3) {
		$term = substr($term, 0, -1);
	}
	$end = substr($term, -3, 3);

	if ("ing" == $end && $len > 5) {
		return substr($term, 0, -3);
	}
	if ("est" == $end && $len > 5) {
		return substr($term, 0, -3);
	}
	
	$end = substr($end, 1);
	if ("es" == $end && $len > 3) {
		return substr($term, 0, -2);
	}
	if ("ed" == $end && $len > 3) {
		return substr($term, 0, -2);
	}
	if ("en" == $end && $len > 3) {
		return substr($term, 0, -2);
	}
	if ("er" == $end && $len > 3) {
		return substr($term, 0, -2);
	}
	if ("ly" == $end && $len > 4) {
		return substr($term, 0, -2);
	}

	return $term;
}

/*
 * Example:
 * 
 * relevanssi_related(get_search_query(), '<h3>Related Searches:</h3><ul><li>', '</li><li>', '</li></ul>');
 * 
 * Function written by John Blackbourn.
 */

function relevanssi_related($query, $pre = '<ul><li>', $sep = '</li><li>', $post = '</li></ul>', $number = 5) {
	global $wpdb, $relevanssi_variables;
	$output = $related = array();
	$tokens = relevanssi_tokenize($query);
	if (empty($tokens))
		return;
	/* Loop over each token in the query and return logged queries which:
	 *
	 *  - Contain a matching token
	 *  - Don't match the query or the token exactly
	 *  - Have at least 2 hits
	 *  - Have been queried at least twice
	 *
	 * then order by most queried with a max of $number results.
	 */
	foreach ($tokens as $token => $count) {
		$sql = $wpdb->prepare("
			SELECT query
			FROM " . $relevanssi_variables['log_table'] . "
			WHERE query LIKE '%%%s%%'
			AND query NOT IN (%s, %s)
			AND hits > 1
			GROUP BY query
			HAVING count(query) > 1
			ORDER BY count(query) DESC
			LIMIT %d
		", $token, $token, $query, $number);
		foreach ($wpdb->get_results($sql) as $result)
			$related[] = $result->query;
	}
	if (empty($related))
		return;
	/* Order results by most matching tokens
	 * then slice to a maximum of $number results:
	 */
	$related = array_keys(array_count_values($related));
	$related = array_slice($related, 0, $number);
	foreach ($related as $rel) {
		$url = add_query_arg(array(
			's' => urlencode($rel)
		), home_url());
		$rel = esc_attr($rel);
		$output[] = "<a href='$url'>$rel</a>";
	}
	echo $pre;
	echo implode($sep, $output);
	echo $post;
}

/*****
 * Interface functions
 */

function relevanssi_import_options($options) {
	$unserialized = unserialize(stripslashes($options));
	foreach ($unserialized as $key => $value) {
		update_option($key, $value);
	}
	
	echo "<div id='relevanssi-warning' class='updated fade'>" . __("Options updated!", "relevanssi") . "</div>";
}

function relevanssi_update_premium_options() {
	if (isset($_REQUEST['relevanssi_link_boost'])) {
		$boost = floatval($_REQUEST['relevanssi_link_boost']);
		update_option('relevanssi_link_boost', $boost);
	}

	if (!isset($_REQUEST['relevanssi_highlight_docs_external'])) {
		$_REQUEST['relevanssi_highlight_docs_external'] = "off";
	}

	if (!isset($_REQUEST['relevanssi_index_subscribers'])) {
		$_REQUEST['relevanssi_index_subscribers'] = "off";
	}

	if (empty($_REQUEST['relevanssi_api_key'])) {
		unset($_REQUEST['relevanssi_api_key']);
	}

	if (!isset($_REQUEST['relevanssi_index_users'])) {
		$_REQUEST['relevanssi_index_users'] = "off";
	}

	if (!isset($_REQUEST['relevanssi_index_synonyms'])) {
		$_REQUEST['relevanssi_index_synonyms'] = "off";
	}

	if (!isset($_REQUEST['relevanssi_index_taxonomies'])) {
		$_REQUEST['relevanssi_index_taxonomies'] = "off";
	}

	if (!isset($_REQUEST['relevanssi_hide_branding'])) {
		$_REQUEST['relevanssi_hide_branding'] = "off";
	}

	if (!isset($_REQUEST['relevanssi_hide_post_controls'])) {
		$_REQUEST['relevanssi_hide_post_controls'] = "off";
	}

	if (isset($_REQUEST['relevanssi_recency_bonus']) && isset($_REQUEST['relevanssi_recency_days'])) {
		$relevanssi_recency_bonus = array();
		$relevanssi_recency_bonus['bonus'] = $_REQUEST['relevanssi_recency_bonus'];
		$relevanssi_recency_bonus['days'] = $_REQUEST['relevanssi_recency_days'];
		update_option('relevanssi_recency_bonus', $relevanssi_recency_bonus);
	}

	if (isset($_REQUEST['relevanssi_api_key'])) update_option('relevanssi_api_key', $_REQUEST['relevanssi_api_key']);
	if (isset($_REQUEST['relevanssi_highlight_docs_external'])) update_option('relevanssi_highlight_docs_external', $_REQUEST['relevanssi_highlight_docs_external']);
	if (isset($_REQUEST['relevanssi_index_synonyms'])) update_option('relevanssi_index_synonyms', $_REQUEST['relevanssi_index_synonyms']);
	if (isset($_REQUEST['relevanssi_index_users'])) update_option('relevanssi_index_users', $_REQUEST['relevanssi_index_users']);
	if (isset($_REQUEST['relevanssi_index_subscribers'])) update_option('relevanssi_index_subscribers', $_REQUEST['relevanssi_index_subscribers']);
	if (isset($_REQUEST['relevanssi_index_user_fields'])) update_option('relevanssi_index_user_fields', $_REQUEST['relevanssi_index_user_fields']);
	if (isset($_REQUEST['relevanssi_internal_links'])) update_option('relevanssi_internal_links', $_REQUEST['relevanssi_internal_links']);
	if (isset($_REQUEST['relevanssi_hide_branding'])) update_option('relevanssi_hide_branding', $_REQUEST['relevanssi_hide_branding']);
	if (isset($_REQUEST['relevanssi_hide_post_controls'])) update_option('relevanssi_hide_post_controls', $_REQUEST['relevanssi_hide_post_controls']);
	if (isset($_REQUEST['relevanssi_index_taxonomies'])) update_option('relevanssi_index_taxonomies', $_REQUEST['relevanssi_index_taxonomies']);
	if (isset($_REQUEST['relevanssi_taxonomies_to_index'])) update_option('relevanssi_taxonomies_to_index', $_REQUEST['relevanssi_taxonomies_to_index']);
	if (isset($_REQUEST['relevanssi_thousand_separator'])) update_option('relevanssi_thousand_separator', $_REQUEST['relevanssi_thousand_separator']);
	if (isset($_REQUEST['relevanssi_disable_shortcodes'])) update_option('relevanssi_disable_shortcodes', $_REQUEST['relevanssi_disable_shortcodes']);
	if (isset($_REQUEST['relevanssi_mysql_columns'])) update_option('relevanssi_mysql_columns', $_REQUEST['relevanssi_mysql_columns']);
}

function relevanssi_form_api_key($api_key) {
?>
	<label for='relevanssi_api_key'><?php _e('Change API key:', 'relevanssi'); ?>
	<input type='text' id='relevanssi_api_key' name='relevanssi_api_key' value='' /></label> <?php (empty($api_key)) ? _e('(No API key set.)') : _e('(API key is set.)'); ?><br />
	<small><?php _e('API key is required to use the automatic update feature. Get yours from Relevanssi.com.', 'relevanssi'); ?></small>

	<br /><br />
<?php
}

function relevanssi_form_internal_links($intlinks_noindex, $intlinks_strip, $intlinks_nostrip) {
?>
	<label for='relevanssi_internal_links'><?php _e("How to index internal links:", "relevanssi"); ?>
	<select name='relevanssi_internal_links' id='relevanssi_internal_links'>
	<option value='noindex' <?php echo $intlinks_noindex ?>><?php _e("No special processing for internal links", "relevanssi"); ?></option>
	<option value='strip' <?php echo $intlinks_strip ?>><?php _e("Index internal links for target documents only", "relevanssi"); ?></option>
	<option value='nostrip' <?php echo $intlinks_nostrip ?>><?php _e("Index internal links for both target and source", "relevanssi"); ?></option>
	</select></label><br />
	<small><?php _e("Internal link anchor tags can be indexed for target document (so the text will match the document the link points to), both target and source or source only (with no extra significance for the links). See Relevanssi Knowledge Base for more details. Changing this option requires reindexing.", 'relevanssi'); ?></small>

	<br /><br />
<?php
}	

function relevanssi_form_hide_post_controls($hide_post_controls) {
?>
	<label for='relevanssi_hide_post_controls'><?php _e("Hide Relevanssi on edit pages:", "relevanssi"); ?>
	<input type='checkbox' name='relevanssi_hide_post_controls' id='relevanssi_hide_post_controls' <?php echo $hide_post_controls ?> /></label><br />
	<small><?php _e("If you check this option, all Relevanssi features are removed from edit pages.", 'relevanssi'); ?></small>
<?php
}

function relevanssi_form_link_weight($link_boost) {
	global $relevanssi_variables;
?>
	<tr>
		<td>
			<?php _e('Internal links', 'relevanssi'); ?> 
		</td>
		<td>
			<input type='text' id='relevanssi_link_boost' name='relevanssi_link_boost' size='4' value='<?php echo $link_boost ?>' />
		</td>
		<td>
			<?php echo $relevanssi_variables['link_boost_default']; ?>
		</td>
	</tr>
<?php
}

function relevanssi_form_post_type_weights($post_type_weights) {
	$post_types = get_post_types(); 
	foreach ($post_types as $type) {
		if ('nav_menu_item' == $type) continue;
		if ('revision' == $type) continue;
		if (isset($post_type_weights[$type])) {
			$value = $post_type_weights[$type];
		}
		else {
			$value = 1;
		}
		$label = sprintf(__("Post type '%s':", 'relevanssi'), $type);
		
		echo <<<EOH
	<tr>
		<td>
			$label 
		</td>
		<td>
			<input type='text' id='relevanssi_weight_$type' name='relevanssi_weight_$type' size='4' value='$value' />
		</td>
		<td>&nbsp;</td>
	</tr>
EOH;
	}
}

function relevanssi_form_taxonomy_weights($post_type_weights) {
	$taxonomies = get_taxonomies('', 'names'); 
	foreach ($taxonomies as $type) {
		if ('nav_menu' == $type) continue;
		if ('post_format' == $type) continue;
		if ('link_category' == $type) continue;
		if (isset($post_type_weights[$type])) {
			$value = $post_type_weights[$type];
		}
		else {
			$value = 1;
		}
		$label = sprintf(__("Taxonomy '%s':", 'relevanssi'), $type);
		
		echo <<<EOH
	<tr>
		<td>
			$label 
		</td>
		<td>
			<input type='text' id='relevanssi_weight_$type' name='relevanssi_weight_$type' size='4' value='$value' />
		</td>
		<td>&nbsp;</td>
	</tr>
EOH;
	}
}

function relevanssi_form_recency($recency_bonus, $recency_bonus_days) {
?>
	<label for='relevanssi_recency_bonus'><?php _e("Weight multiplier for new posts:", "relevanssi"); ?>
	<input type='text' id='relevanssi_recency_bonus' name='relevanssi_recency_bonus' size='4' value="<?php echo $recency_bonus ?>" /></label><br />
	<label for='relevanssi_recency_days'><?php _e("Assign bonus for posts newer than:", "relevanssi"); ?>
	<input type='text' id='relevanssi_recency_days' name='relevanssi_recency_days' size='4' value='<?php echo $recency_bonus_days ?>' /> <?php _e("days", "relevanssi"); ?></label><br />
	<small><?php _e('Posts newer than the day cutoff specified here will have their weight multiplied with the bonus above.', 'relevanssi'); ?></small>
<?php
}

function relevanssi_form_hide_branding($hide_branding) {
?>
	<label for='relevanssi_hide_branding'><?php _e("Don't show Relevanssi branding on the 'User Searches' screen:", "relevanssi"); ?>
	<input type='checkbox' id='relevanssi_hide_branding' name='relevanssi_hide_branding' <?php echo $hide_branding ?> /></label>
<?php
}

function relevanssi_form_highlight_external($highlight_docs_ext) {
?>
	<label for='relevanssi_highlight_docs_external'><?php _e("Highlight query terms in documents from external searches:", 'relevanssi'); ?>
	<input type='checkbox' id='relevanssi_highlight_docs_external' name='relevanssi_highlight_docs_external' <?php echo $highlight_docs_ext ?> /></label>
	<small><?php _e("Highlights hits when user arrives from external search. Currently supports Bing, Ask, Yahoo and AOL Search.", "relevanssi"); ?></small>

	<br />
<?php
}

function relevanssi_form_thousep($thousand_separator) {
?>
	<label for='relevanssi_thousand_separator'><?php _e("Thousands separator", "relevanssi"); ?>:
	<input type='text' name='relevanssi_thousand_separator' id='relevanssi_thousand_separator' size='30' value='<?php echo $thousand_separator ?>' /></label><br />
	<small><?php _e("If Relevanssi sees this character between numbers, it'll stick the numbers together no matter how the character would otherwise be handled. Especially useful if a space is used as a thousands separator.", "relevanssi"); ?></small>

	<br /><br />
<?php
}

function relevanssi_form_disable_shortcodes($disable_shortcodes) {
?>
	<label for='relevanssi_disable_shortcodes'><?php _e("Disable these shortcodes", "relevanssi"); ?>:
	<input type='text' name='relevanssi_disable_shortcodes' id='relevanssi_disable_shortcodes' size='30' value='<?php echo $disable_shortcodes ?>' /></label><br />
	<small><?php _e("These shortcodes will not be expanded if expand shortcodes above is enabled. This is useful if a particular shortcode is causing problems in indexing.", "relevanssi"); ?></small>

	<br /><br />
<?php
}

function relevanssi_form_mysql_columns($mysql_columns) {
	global $wpdb;
	$column_list = $wpdb->get_results("SHOW COLUMNS FROM $wpdb->posts");
	$columns = array();
	foreach ($column_list as $column) {
		array_push($columns, $column->Field);
	}
	$columns = implode(', ', $columns);
	
?>
	<label for='relevanssi_mysql_columns'><?php _e("Custom MySQL columns to index:", "relevanssi"); ?>
	<input type='text' name='relevanssi_mysql_columns' id='relevanssi_mysql_columns' size='30' value='<?php echo $mysql_columns ?>' /></label><br />
	<small><?php _e("A comma-separated list of wp_posts MySQL table columns to include in the index. Following columns are available: ", "relevanssi"); echo $columns; ?>.</small>

	<br /><br />
<?php
}

function relevanssi_form_index_users($index_users, $index_subscribers, $index_user_fields) {
?>
	<label for='relevanssi_index_users'><?php _e('Index and search user profiles:', 'relevanssi'); ?>
	<input type='checkbox' name='relevanssi_index_users' id='relevanssi_index_users' <?php echo $index_users ?> /></label><br />
	<small><?php _e("If checked, Relevanssi will also index and search user profiles (first name, last name, display name and user description). Requires changes to search results template, see Relevanssi Knowledge Base.", 'relevanssi'); ?></small>

	<br /><br />

	<label for='relevanssi_index_subscribers'><?php _e('Index subscriber profiles:', 'relevanssi'); ?>
	<input type='checkbox' name='relevanssi_index_subscribers' id='relevanssi_index_subscribers' <?php echo $index_subscribers ?> /></label><br />
	<small><?php _e("If checked, Relevanssi will index subscriber profiles as well, otherwise only authors, editors, contributors and admins are indexed.", 'relevanssi'); ?></small>

	<br /><br />

	<label for='relevanssi_index_user_fields'><?php _e("Extra user fields to index:", "relevanssi"); ?>
	<input type='text' name='relevanssi_index_user_fields' id='relevanssi_index_user_fields' size='30' value='<?php echo $index_user_fields ?>' /></label><br />
	<small><?php _e("A comma-separated list of user profile field names (names of the database columns) to include in the index.", "relevanssi"); ?></small>

	<br /><br />
<?php
}

function relevanssi_form_index_synonyms($index_synonyms) {
?>
	<label for='relevanssi_index_synonyms'><?php _e('Index synonyms:', 'relevanssi'); ?>
	<input type='checkbox' name='relevanssi_index_synonyms' id='relevanssi_index_synonyms' <?php echo $index_synonyms ?> /></label><br />
	<small><?php _e("If checked, Relevanssi will use the synonyms in indexing. If you add 'apple=pear' to the synonym list and enable this feature, every time the indexer sees 'pear' it will index it both as 'apple' and as 'pear'. Thus, the post will be found when searching with either word. This makes it possible to use synonyms with AND searches, but will slow down indexing, especially with large databases and large lists of synonyms. This only works for post titles and post content. You can use multi-word keys and values, but phrases do not work.", 'relevanssi'); ?></small>

	<br /><br />
<?php
}

function relevanssi_form_index_taxonomies($index_taxonomies, $index_terms) {
?>
	<label for='relevanssi_index_taxonomies'><?php _e('Index and search taxonomy pages:', 'relevanssi'); ?>
	<input type='checkbox' name='relevanssi_index_taxonomies' id='relevanssi_index_taxonomies' <?php echo $index_taxonomies ?> /></label><br />
	<small><?php _e("If checked, Relevanssi will also index and search taxonomy pages (categories, tags, custom taxonomies).", 'relevanssi'); ?></small>

	<br /><br />

	<p><?php _e('Choose taxonomies to index – for these taxonomies, the terms are included in search results:', 'relevanssi'); ?></p>
	
	<table class="widefat" id="index_terms_table">
	<thead>
		<tr>
			<th><?php _e('Taxonomy', 'relevanssi'); ?></th>
			<th><?php _e('Index', 'relevanssi'); ?></th>
			<th><?php _e('Public?', 'relevanssi'); ?></th>
		</tr>
	</thead>
	<?php
		$taxos = get_taxonomies('', 'objects');
		foreach ($taxos as $taxonomy) {
			if ($taxonomy->name == 'nav_menu') continue;
			if ($taxonomy->name == 'link_category') continue;
			if (in_array($taxonomy->name, $index_terms)) {
				$checked = 'checked="checked"';
			}
			else {
				$checked = '';
			}
			$label = sprintf(__("%s", 'relevanssi'), $taxonomy->name);
			$taxonomy->public ? $public = __('yes', 'relevanssi') : $public = __('no', 'relevanssi');
			$type = $taxonomy->name;
					
			echo <<<EOH
	<tr>
		<td>
			$label 
		</td>
		<td>
			<input type='checkbox' name='relevanssi_index_terms_$type' id='relevanssi_index_terms_$type' $checked />
		</td>
		<td>
			$public
		</td>
	</tr>
EOH;
		}
	?>
	</table>
	
	<p><?php _e('If you check a taxonomy here, the terms in that taxonomy will be indexed and will appear as itself in the search results.', 'relevanssi'); ?>
	
	<br /><br />
<?php
}

function relevanssi_form_importexport($serialized_options) {
?>
	<h3 id="options"><?php _e("Import or export options", "relevanssi"); ?></h3>
	
	<p><?php _e("Here you find the current Relevanssi Premium options in a text format. Copy the contents of the text field to make a backup of your settings. You can also paste new settings here to change all settings at the same time. This is useful if you have default settings you want to use on every system.", "relevanssi"); ?></p>
	
	<p><textarea name='relevanssi_settings' rows='2' cols='60'><?php echo $serialized_options; ?></textarea></p>

	<input type='submit' name='import_options' id='import_options' value='<?php _e("Import settings", 'relevanssi'); ?>' class='button' />

	<p><?php _e("Note! Make sure you've got correct settings from a right version of Relevanssi. Settings from a different version of Relevanssi may or may not work and may or may not mess your settings.", "relevanssi"); ?></p>
<?php
}

function relevanssi_sidebar() {
	if (function_exists("plugins_url")) {
		global $wp_version;
		if (version_compare($wp_version, '2.8dev', '>' )) {
			$facebooklogo = plugins_url('facebooklogo.jpg', __FILE__);
		}
		else {
			$facebooklogo = plugins_url('relevanssi-premium/facebooklogo.jpg');
		}
	}
	else {
		// We can't check, so let's assume something sensible
		$facebooklogo = '/wp-content/plugins/relevanssi-premium/facebooklogo.jpg';
	}

	$thankyou = __('Thank you!', 'relevanssi');
	$text1 = __('Thank you for buying Relevanssi Premium! Your support makes it possible for me to keep working on this plugin.', 'relevanssi');
	$text2 = __('I can do custom hacks based on Relevanssi and other WordPress development. If you need someone to fix your WordPress, just ask me for a quote.');
	
	$facebook = __('Relevanssi on Facebook', 'relevanssi');
	$facebook_anchor = __('Check out the Relevanssi page on Facebook', 'relevanssi');
	$facebook_rest = __('for news and updates about your favourite plugin.', 'relevanssi');
	
	$help = __('Help and support', 'relevanssi');
	$support1 = __('For Relevanssi support, see:', 'relevanssi');
	$support2 = __('Plugin support page', 'relevanssi');
	$support3 = __('WordPress.org forum', 'relevanssi');
	
	$feature = __('Did you know this feature?', 'relevanssi');
	$feature1 = __('Wrap the parts of the posts you don\'t want to include in the index in [noindex] shortcode.', 'relevanssi'); 
	$feature2 = __('Use the [search] shortcode to build easy links to search results.', 'relevanssi');
	$feature3 = __('Enable the English-language stemmer by adding this line in your functions.php:', 'relevanssi');
	$feature4 = __('Boolean NOT', 'relevanssi');
	$feature5 = __('To get results without particular word, use the minus operator', 'relevanssi');
	$example1 = __('cats -dogs', 'relevanssi');
	$feature6 = __('Boolean AND', 'relevanssi');
	$feature7 = __('To force a particular term in an OR search, use the plus operator', 'relevanssi');
	$example2 = __('+cats dogs mice', 'relevanssi');
	$feature8 = __('would require that all results include the term \'cats\', and results including all three terms will be favoured. The plus operator has no effect in an AND search, where all terms have an implicit + before them.', 'relevanssi');
	
	echo <<<EOH
<div class="postbox-container" style="width:20%; margin-top: 35px; margin-left: 15px;">
	<div class="metabox-holder">	
		<div class="meta-box-sortables" style="min-height: 0">
			<div id="relevanssi_donate" class="postbox">
			<h3 class="hndle"><span>$thankyou</span></h3>
			<div class="inside">
			<p>$text1</p>
			<p>$text2</p>
			</div>
		</div>
	</div>

		<div class="meta-box-sortables" style="min-height: 0">
			<div id="relevanssi_donate" class="postbox">
			<h3 class="hndle"><span>$facebook</span></h3>
			<div class="inside">
			<div style="float: left; margin-right: 5px"><img src="$facebooklogo" width="45" height="43" alt="Facebook" /></div>
			<p><a href="http://www.facebook.com/relevanssi">$facebook_anchor</a> $facebook_rest</p>
			</div>
		</div>
	</div>

		<div class="meta-box-sortables" style="min-height: 0">
			<div id="relevanssi_donate" class="postbox">
			<h3 class="hndle"><span>$help</span></h3>
			<div class="inside">
			<p>$support1</p>
			
			<p>
			- <a href="http://www.relevanssi.com/support/">$support2</a><br />
			- <a href="http://wordpress.org/tags/relevanssi?forum_id=10">$support3</a><br />
			- support@relevanssi.zendesk.com
			</p>
			</div>
		</div>
	</div>

		<div class="meta-box-sortables" style="min-height: 0">
			<div id="relevanssi_donate" class="postbox">
			<h3 class="hndle"><span>$feature</span></h3>
			<div class="inside">
			<p><strong>[noindex]</strong></p>
			
			<p>$feature1</p>

			<p><strong>[search]</strong></p>
			
			<p>$feature2</p>
			
			<p><strong>Stemmer</strong></p>
			
			<p>$feature3</p>
			
			<p>add_filter('relevanssi_stemmer', 'relevanssi_simple_english_stemmer');</p>

			<p><strong>$feature4</strong></p>
			
			<p>$feature5</p>
			
			<p><em>$example1</em></p>

			<p><strong>$feature6</strong></p>
			
			<p>$feature7</p>
			
			<p><em>$example2</em></p>
			
			<p>$feature8</p>
			</div>
		</div>
	</div>
</div>
</div>
EOH;
}

function relevanssi_add_metaboxes() {
	global $post;
	if ($post->post_type == 'acf') return; 		// no metaboxes for Advanced Custom Fields pages
	add_meta_box( 
        'relevanssi_hidebox',
        __( 'Relevanssi post controls', 'relevanssi' ),
    	'relevanssi_post_metabox',
     	$post->post_type
   	 );
}

function relevanssi_post_metabox() {
	wp_nonce_field(plugin_basename(__FILE__), 'relevanssi_hidepost');

	global $post;
	$check = checked('on', get_post_meta($post->ID, '_relevanssi_hide_post', true), false);
	
	$pins = get_post_meta($post->ID, '_relevanssi_pin', false);
	$pin = implode(', ', $pins);
	
	// The actual fields for data entry
	echo '<input type="checkbox" id="relevanssi_hide_post" name="relevanssi_hide_post" ' . $check . ' />';
	echo ' <label for="relevanssi_hide_post">';
	_e("Exclude this post or page from the index.", 'relevanssi');
	echo '</label> ';
	
	echo '<p><strong>' . __('Pin this post', 'relevanssi') . '</strong></p>';
	echo '<p>' . __('A comma-separated list of single word keywords. If any of these keywords are present in the search query, this post will be moved on top of the search results.') . '</p>';
	echo '<input type="text" id="relevanssi_pin" name="relevanssi_pin" value="' . $pin . '"/>';
	
	
}
function relevanssi_save_postdata($post_id) {
	// verify if this is an auto save routine. 
	// If it is our form has not been submitted, so we dont want to do anything
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
		return;

	if (isset($_POST['relevanssi_hidepost'])) {
		if (!wp_verify_nonce($_POST['relevanssi_hidepost'], plugin_basename( __FILE__ )))
			return;
	}

	// Check permissions
	if (isset($_POST['post_type'])) {
		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id))
				return;
		}
		else {
			if (!current_user_can('edit_post', $post_id))
				return;
		}
	}

	isset($_POST['relevanssi_hide_post']) ? $hide = $_POST['relevanssi_hide_post'] : $hide = '';

	if ('on' == $hide) {
		relevanssi_delete($post_id);
	}

	$hide == 'on' ?
		update_post_meta($post_id, '_relevanssi_hide_post', $hide) :
		delete_post_meta($post_id, '_relevanssi_hide_post');

	if (isset($_POST['relevanssi_pin'])) {
		delete_post_meta($post_id, '_relevanssi_pin');
		$pins = explode(',', $_POST['relevanssi_pin']);
		foreach ($pins as $pin) {
			$pin = trim($pin);
			add_post_meta($post_id, '_relevanssi_pin', $pin);
		}
	}
	else {
		delete_post_meta($post_id, '_relevanssi_pin');
	}
}

function relevanssi_get_words() {
	global $wpdb, $relevanssi_variables;
	
	$q = "SELECT term, title + content + comment + tag + link + author + category + excerpt + taxonomy + customfield as c FROM " . $relevanssi_variables['relevanssi_table'] . " GROUP BY term";
	$q = apply_filters('relevanssi_get_words_query', $q);
	$results = $wpdb->get_results($q);
	
	$words = array();
	foreach ($results as $result) {
		$words[$result->term] = $result->c;
	}
	
	return $words;
}

function relevanssi_index_synonyms($post) {
	global $relevanssi_variables;
	
	if (!isset($relevanssi_variables['synonyms'])) relevanssi_create_synonym_replacement_array();

	$search = array_keys($relevanssi_variables['synonyms']);
	$replace = array_values($relevanssi_variables['synonyms']);

	function_exists('mb_strtolower') ? $post_content = mb_strtolower($post->post_content) : $post_content = strtolower($post->post_content);
	function_exists('mb_strtolower') ? $post_title = mb_strtolower($post->post_title) : $post_title = strtolower($post->post_title);

	$post->post_content = str_replace($search, $replace, $post_content);
	$post->post_title = str_replace($search, $replace, $post_title);
	
	return $post;
}

function relevanssi_create_synonym_replacement_array() {
	global $relevanssi_variables;
	
	$synonym_data = get_option('relevanssi_synonyms');
	if ($synonym_data) {
		$synonyms = array();
		if (function_exists('mb_strtolower')) {
			$synonym_data = mb_strtolower($synonym_data);
		}
		else {
			$synonym_data = strtolower($synonym_data);
		}
		$pairs = explode(";", $synonym_data);
		foreach ($pairs as $pair) {
			$parts = explode("=", $pair);
			$key = strval(trim($parts[0]));
			$value = trim($parts[1]);
			if (!isset($synonyms[$value])) {
				$synonyms[$value] = "$value $key";
			}
			else {
				$synonyms[$value] .= " $key";
			}
		}
		$relevanssi_variables['synonyms'] = $synonyms;
	}
}

function relevanssi_wpmu_drop($tables) {
	global $relevanssi_variables;
	$tables[] = $relevanssi_variables['relevanssi_table'];
	$tables[] = $relevanssi_variables['stopword_table'];
	$tables[] = $relevanssi_variables['log_table'];
	$tables[] = $relevanssi_variables['relevanssi_cache'];
	$tables[] = $relevanssi_variables['relevanssi_excerpt_cache'];
	return $tables;
}

add_filter('relevanssi_hits_filter', 'relevanssi_pinning');
function relevanssi_pinning($hits) {
	global $wpdb;
	$terms = relevanssi_tokenize($hits[1], false);
	$escaped_terms = array();
	foreach (array_keys($terms) as $term) {
		$escaped_terms[] = esc_sql($term);
	}
	$term_list = implode("','", $escaped_terms);
	$term_list = "'$term_list'";
	$q = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_relevanssi_pin' AND meta_value IN ($term_list)";
	$matching_ids = $wpdb->get_col($q);
	
	if (is_array($matching_ids) && count($matching_ids) > 0) {
		$pinned_posts = array();
		$other_posts = array();
		foreach ($hits[0] as $hit) {
			if (in_array($hit->ID, $matching_ids)) {
				$pinned_posts[] = $hit;
			}
			else {
				$other_posts[] = $hit;
			}
		}
		$hits[0] = array_merge($pinned_posts, $other_posts);
	}
	return $hits;
}

add_filter('relevanssi_content_to_index', 'relevanssi_index_pinning_words', 10, 2);
function relevanssi_index_pinning_words($content, $post) {
	$pin_words = get_post_meta($post->ID, '_relevanssi_pin', false);
	foreach ($pin_words as $word) {
		$content .= " $word";
	}
	return $content;
}

?>