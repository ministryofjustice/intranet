<?php

/**
 * PHP Markdown Extra plugin customizations
 *
 * The plugin transforms excerpts on get_the_excerpt, which wraps them in <p>
 * tags. WP_Posts_List_Table prints the excerpt through esc_html(), so on post
 * listings in excerpt mode that markup is shown to editors as literal text:
 *
 *     &lt;p&gt;Anisa Ismail, Judicial College, shares her...&lt;/p&gt;
 *
 * The stored post_excerpt is plain text; only the filtered value carries the
 * markup, so this is display-only and nothing needs correcting in the database.
 *
 * @package Clarity
 */

/**
 * Strip markup from excerpts shown on post listing screens.
 *
 * Runs after the plugin's own transform (priority 6) and wp_trim_excerpt (10).
 * Limited to edit.php so the front end keeps its formatting.
 *
 * @param string $excerpt
 * @return string
 */
function clarity_plain_text_excerpt_in_list_table($excerpt)
{
    global $pagenow;

    if (!is_admin() || $pagenow !== 'edit.php') {
        return $excerpt;
    }

    return wp_strip_all_tags($excerpt);
}

add_filter('get_the_excerpt', 'clarity_plain_text_excerpt_in_list_table', 20);
