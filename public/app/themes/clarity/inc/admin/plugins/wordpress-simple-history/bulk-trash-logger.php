<?php

/**
 * Simple History logger for core's bulk "Move to Bin" on the post listings.
 *
 * Loaded by SimpleHistory::registerLoggers() once the plugin has asked for
 * custom loggers, so the plugin's classes are known to exist by then.
 *
 * @package Clarity
 **/

namespace MOJ\Intranet\Loggers;

use Simple_History\Loggers\Logger;
use Simple_History\Event_Details\Event_Details_Group;
use Simple_History\Event_Details\Event_Details_Item;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Records a bulk "Move to Bin" on the post listings as one event, listing the
 * post IDs, so that a run can be reverted from the log alone.
 *
 * Core handles the action itself in edit.php, post by post, with no hook for
 * the run as a whole, so the row is written as edit.php loads, from the IDs in
 * the request, and says only what was asked for. It is written before core's
 * loop starts because trashing is not transactional: a request that dies part
 * way through leaves the posts already handled in the bin, and a row written
 * afterwards would never appear. What was actually binned is in the plugin's
 * own Post logger, which keeps writing one row per post.
 *
 * Reverting a run
 * ---------------
 * Open the event in Simple History and follow "Show post IDs", or read the
 * `post_ids` context row for its history_id from the wp_simple_history_contexts
 * table. Restore the ones that are in the bin, which the per post rows that
 * follow also list. Each post keeps its previous status in the
 * _wp_trash_meta_status meta while binned, and the filter below puts it back,
 * where wp_untrash_post() alone would restore to draft. The bin is emptied for
 * good after EMPTY_TRASH_DAYS, 30 by default, so this has to happen before then:
 *
 *     wp eval 'add_filter("wp_untrash_post_status", fn($s, $id, $prev) => $prev, 10, 3);
 *              foreach ([<post_id>, <post_id>] as $id) wp_untrash_post($id);'
 *
 * The user who ran the action is recorded by Simple History itself, in the
 * `_user_id`, `_user_login` and `_user_email` context values.
 */
class Bulk_Trash_Logger extends Logger
{
    /**
     * Logger slug. At most 30 characters, which is the width of the column.
     *
     * @var string
     */
    public $slug = 'MOJBulkTrashLogger';

    /**
     * Return info about this logger.
     *
     * @return array
     */
    public function get_info()
    {
        return [
            'name'        => __('Bulk move to bin logger'),
            'description' => __('Logs a bulk move to the bin on the post listings as one event'),
            'name_via'    => __('Using the post list bulk action'),
            'capability'  => 'manage_options',
            'messages'    => [
                'bulk_trash' => _x(
                    'Started moving {post_count} {post_type_label} to the bin',
                    'Bulk move to bin logger: started'
                ),
            ],
            'labels'      => [
                'search' => [
                    'label'     => _x('Bulk move to bin', 'Bulk move to bin logger: search'),
                    'label_all' => _x('All bulk moves to the bin', 'Bulk move to bin logger: search'),
                    'options'   => [
                        _x('Posts moved to the bin in bulk', 'Bulk move to bin logger: search') => ['bulk_trash'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function loaded()
    {
        // Before edit.php runs, which is where core processes its bulk actions.
        add_action('load-edit.php', [$this, 'on_load_edit']);
    }

    /**
     * A post listing is loading. If the request is a bulk "Move to Bin",
     * record it before core acts on it.
     *
     * Mirrors how edit.php reads the request: WP_List_Table::current_action()
     * for the action, then the same precedence for the post IDs. Only the
     * "action" field is read, as core does: the bottom selector's "action2"
     * is kept in step with the top one by common.js, and a request carrying
     * only action2 does nothing in core, so there is nothing to record. The
     * nonce is checked here too, without dying, so that a request core is
     * about to reject is not recorded as if it had happened.
     *
     * @return void
     */
    public function on_load_edit()
    {
        global $typenow;

        if (!empty($_REQUEST['filter_action']) || ($_REQUEST['action'] ?? '-1') !== 'trash') {
            return;
        }

        if (isset($_REQUEST['media'])) {
            // Attachments belong to upload.php, not the post listings.
            return;
        }

        if (isset($_REQUEST['ids']) && is_scalar($_REQUEST['ids'])) {
            $ids = explode(',', (string) $_REQUEST['ids']);
        } elseif (!empty($_REQUEST['post']) && is_array($_REQUEST['post'])) {
            $ids = $_REQUEST['post'];
        } else {
            return;
        }

        $post_ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (empty($post_ids) || !wp_verify_nonce($_REQUEST['_wpnonce'] ?? '', 'bulk-posts')) {
            return;
        }

        $post_type = (string) $typenow;
        $object = get_post_type_object($post_type);
        $label = $object
            ? (count($post_ids) === 1 ? $object->labels->singular_name : $object->labels->name)
            : $post_type;

        // Every value is stored as a string, so the ID list is joined with commas.
        $this->info_message('bulk_trash', [
            'post_type'       => $post_type,
            'post_type_label' => strtolower($label),
            'post_ids'        => implode(',', $post_ids),
            'post_count'      => count($post_ids),
        ]);
    }

    /**
     * Offer the "Show details" link, since the ID list is what the event is for.
     *
     * @param object $row
     * @return string|false
     */
    public function event_has_more_details($row)
    {
        return __('Show post IDs');
    }

    /**
     * The details table: post type, the ID list and the count.
     *
     * @param object $row
     * @return Event_Details_Group
     */
    public function get_log_row_details_output($row)
    {
        $group = new Event_Details_Group();

        $group->add_items([
            new Event_Details_Item('post_type', __('Post type')),
            new Event_Details_Item('post_ids', __('Post IDs to bin')),
            new Event_Details_Item('post_count', __('Posts selected')),
        ]);

        return $group;
    }
}
