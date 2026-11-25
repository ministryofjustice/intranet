<?php

/**
 * CMS Tree Page View plugin customizations
 *
 * Keeps the object cache consistent after drag-and-drop moves in the tree.
 * The normalization logic for duplicate menu_order values is in the plugin itself.
 *
 * Background: for "before" and "after" moves the plugin shifts the siblings with a
 * raw `UPDATE ... SET menu_order = menu_order + 1` (see cms_tpv_move_page()), which
 * bypasses WordPress, so the siblings' cached post objects keep their old menu_order.
 * The next move then reads that stale value from $post_ref_node->menu_order and
 * places pages in the wrong position.
 *
 * This previously flushed the entire object cache on every tree load and after every
 * move. That wiped the cache site-wide (front end included) each time an editor opened
 * Pages > Tree View, and forced the next requests to rebuild it, so it is replaced by
 * invalidating only the posts the move touched.
 *
 * @package Clarity
 */

/**
 * Remember which sibling group a "before"/"after" move is about to renumber.
 *
 * Fires after validation and duplicate normalization, immediately before the plugin
 * writes. "inside" moves only call wp_update_post() on the moved page, which clears
 * its own cache, so they need nothing extra.
 *
 * @param WP_Post $post_node     The page being moved.
 * @param WP_Post $post_ref_node The reference page.
 * @param string  $type          'before', 'after' or 'inside'.
 */
add_action('cms_tree_page_view_before_move', function ($post_node, $post_ref_node, $type) {
    if (!in_array($type, ['before', 'after'], true)) {
        return;
    }

    $GLOBALS['clarity_cms_tpv_renumbered_siblings'] = [
        'post_type'   => $post_ref_node->post_type,
        'post_parent' => (int) $post_ref_node->post_parent,
    ];
}, 10, 3);

/**
 * After a move, drop the cached objects of the siblings whose menu_order the plugin
 * changed with raw SQL.
 *
 * Only menu_order changes, so only the `posts` cache entries are deleted (meta, terms
 * and post_parent caches are still correct). Every sibling in the group is cleared,
 * matching the plugin's UPDATE (same post_type and post_parent, any status), which is
 * at most a few hundred keys. Bumping posts last_changed invalidates cached queries,
 * such as the tree's get_posts() calls.
 */
add_action('cms_tree_page_view_node_move_finish', function () {
    $group = $GLOBALS['clarity_cms_tpv_renumbered_siblings'] ?? null;
    unset($GLOBALS['clarity_cms_tpv_renumbered_siblings']);

    if (!$group) {
        return;
    }

    global $wpdb;

    $sibling_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_parent = %d",
        $group['post_type'],
        $group['post_parent']
    ));

    wp_cache_delete_multiple(array_map('intval', $sibling_ids), 'posts');
    wp_cache_set_posts_last_changed();
});
