<?php

/**
 * Simple History logger for the theme's "Remove <agency> tag" bulk action.
 *
 * Loaded by SimpleHistory::registerLoggers() once the plugin has asked for
 * custom loggers, so the plugin's classes are known to exist by then.
 *
 * @package Clarity
 **/

namespace MOJ\Intranet\Loggers;

use Simple_History\Helpers;
use Simple_History\Loggers\Logger;
use Simple_History\Event_Details\Event_Details_Group;
use Simple_History\Event_Details\Event_Details_Item;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Records the "Remove <agency> tag" bulk action on the post listings.
 *
 * One event is written per run of the action, in two steps. The row is
 * inserted before the first tag is removed, naming every post about to lose
 * it, and the outcome is appended to the same row once the removals are done.
 * The early insert exists for the case where the request dies part way
 * through: the term removals are not transactional, so the posts handled
 * before the failure stay untagged, and without it there would be no record
 * of which ones they could be. A row with no outcome is such a run, and is
 * shown as one.
 *
 * Reverting a run
 * ---------------
 * Open the event in Simple History and follow "Show post IDs", or read the
 * context rows for its history_id from the wp_simple_history_contexts table.
 * Take the `removed_ids` and `agency_slug` values. If the row has no
 * `removed_ids`, the request died mid-run: take `post_ids` instead, which
 * lists every post that was going to be untagged, and restore only the ones
 * that no longer carry the tag. Then, for each post ID:
 *
 *     wp post term add <post_id> agency <agency_slug>
 *
 * The user who ran the action is recorded by Simple History itself, in the
 * `_user_id`, `_user_login` and `_user_email` context values.
 */
class Agency_Tag_Logger extends Logger
{
    /**
     * Logger slug. At most 30 characters, which is the width of the column.
     *
     * @var string
     */
    public $slug = 'MOJAgencyTagLogger';

    /**
     * The row inserted when the current run started, awaiting its outcome.
     *
     * @var int|null
     */
    protected $pending_history_id = null;

    /**
     * The "only" count when the current run started, so the outcome can say
     * how many posts had their tag put back during the removals.
     *
     * @var int
     */
    protected $pending_only = 0;

    /**
     * Return info about this logger.
     *
     * The message is what the row says when the run has finished. A row
     * without an outcome is worded differently by get_log_row_plain_text_output().
     *
     * @return array
     */
    public function get_info()
    {
        return [
            'name'        => __('Agency tag logger'),
            'description' => __('Logs the bulk removal of an agency tag from posts'),
            'name_via'    => __('Using the post list bulk action'),
            'capability'  => 'manage_options',
            'messages'    => [
                'agency_tag_removal' => _x(
                    'Removed the "{agency_name}" agency tag from {removed_count} of {post_count} {post_type_label}',
                    'Agency tag logger: removal'
                ),
            ],
            'labels'      => [
                'search' => [
                    'label'     => _x('Agency tags', 'Agency tag logger: search'),
                    'label_all' => _x('All agency tag removals', 'Agency tag logger: search'),
                    'options'   => [
                        _x('Agency tag removed from posts', 'Agency tag logger: search') => ['agency_tag_removal'],
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
        add_action('clarity/agency_scope/agency_tag_removal_started', [$this, 'on_removal_started']);
        add_action('clarity/agency_scope/agency_tag_removal_finished', [$this, 'on_removal_finished']);
    }

    /**
     * The bulk action is about to remove the tag from a list of posts.
     *
     * Inserts the row and remembers its ID for the outcome.
     *
     * @param array $details See Agency_Scope::handle_bulk_action().
     * @return void
     */
    public function on_removal_started($details)
    {
        $this->info_message('agency_tag_removal', $this->context_from_details($details));

        $this->pending_history_id = $this->last_insert_id ?: null;
        $this->pending_only = (int) ($details['only'] ?? 0);
    }

    /**
     * The bulk action has finished.
     *
     * Appends the outcome to the row inserted when the run started. If no row
     * is pending, which would mean the started action never reached this
     * logger, a complete row is written instead so the outcome is not lost.
     *
     * @param array $details See Agency_Scope::handle_bulk_action().
     * @return void
     */
    public function on_removal_finished($details)
    {
        $outcome = $this->outcome_from_details($details);

        if ($this->pending_history_id) {
            $this->append_context($this->pending_history_id, $outcome);
            $this->pending_history_id = null;
            $this->pending_only = 0;
            return;
        }

        // Without the started step, post_ids would be the removed list. Keep
        // the two apart so the docblock's revert recipe still reads true.
        $context = array_merge(
            $this->context_from_details(['post_ids' => []] + $details),
            $outcome
        );

        $this->info_message('agency_tag_removal', $context);
    }

    /**
     * The context written when the run starts.
     *
     * Every value is stored as a string, so the ID list is joined with commas.
     *
     * @param array $details
     * @return array
     */
    protected function context_from_details($details)
    {
        $post_ids = array_map('intval', (array) ($details['post_ids'] ?? []));
        $post_type = $details['post_type'] ?? '';

        return [
            'agency_term_id'    => (int) ($details['term_id'] ?? 0),
            'agency_slug'       => $details['agency_slug'] ?? '',
            'agency_name'       => $details['agency_name'] ?? '',
            'post_type'         => $post_type,
            'post_type_label'   => $this->post_type_label($post_type, count($post_ids)),
            'post_ids'          => implode(',', $post_ids),
            'post_count'        => count($post_ids),
            'selected_count'    => (int) ($details['selected'] ?? 0),
            'skipped_only'      => (int) ($details['only'] ?? 0),
            'skipped_denied'    => (int) ($details['denied'] ?? 0),
            'skipped_unchanged' => (int) ($details['unchanged'] ?? 0),
        ];
    }

    /**
     * The context appended when the run finishes.
     *
     * Keys are distinct from those written at the start, since appending does
     * not replace: a repeated key would leave two rows in the contexts table.
     * The "only" count can grow during the removals, when a post is found to
     * have lost its other tags in the meantime and gets this one back. That
     * growth is recorded here as the number of posts restored.
     *
     * @param array $details
     * @return array
     */
    protected function outcome_from_details($details)
    {
        $removed_ids = array_map('intval', (array) ($details['post_ids'] ?? []));
        $failed_ids = array_map('intval', (array) ($details['failed_ids'] ?? []));

        return [
            'removed_ids'   => implode(',', $removed_ids),
            'removed_count' => (int) ($details['removed'] ?? count($removed_ids)),
            'failed_ids'    => implode(',', $failed_ids),
            'failed_count'  => (int) ($details['failed'] ?? count($failed_ids)),
            'restored_count' => max(0, (int) ($details['only'] ?? 0) - $this->pending_only),
        ];
    }

    /**
     * A lower case post type label to follow a count: "3 news articles".
     *
     * @param string $post_type
     * @param int $count
     * @return string
     */
    protected function post_type_label($post_type, $count)
    {
        $object = get_post_type_object($post_type);

        if (!$object) {
            return $post_type;
        }

        $label = $count === 1 ? $object->labels->singular_name : $object->labels->name;

        return strtolower($label);
    }

    /**
     * Has the run recorded its outcome?
     *
     * @param object $row
     * @return bool
     */
    protected function is_finished($row)
    {
        return isset($row->context['removed_count']);
    }

    /**
     * Word the row for what actually happened.
     *
     * The message in get_info() describes a finished run. A row without an
     * outcome is a run that died before it could append one, and reads so.
     *
     * @param object $row
     * @return string
     */
    public function get_log_row_plain_text_output($row)
    {
        if ($this->is_finished($row)) {
            return parent::get_log_row_plain_text_output($row);
        }

        $message = _x(
            'Started removing the "{agency_name}" agency tag from {post_count} {post_type_label}, but did not finish',
            'Agency tag logger: removal did not finish'
        );

        // Escaped like the parent does, since the admin renders this as HTML.
        return esc_html(Helpers::interpolate($message, $row->context, $row));
    }

    /**
     * Offer the "Show details" link, since the ID lists are what the event is for.
     *
     * @param object $row
     * @return string|false
     */
    public function event_has_more_details($row)
    {
        return __('Show post IDs');
    }

    /**
     * The details table: agency, post type, the ID lists and the counts.
     *
     * Items whose context value is empty are dropped by Simple History, so
     * the failed IDs row only shows when there were any.
     *
     * @param object $row
     * @return Event_Details_Group
     */
    public function get_log_row_details_output($row)
    {
        $status = (new Event_Details_Item(null, __('Status')))
            ->set_new_value(
                $this->is_finished($row)
                    ? __('Finished')
                    : __('Did not finish: check which of the posts to untag still carry the tag')
            );

        $group = new Event_Details_Group();

        $group->add_items([
            $status,
            new Event_Details_Item('agency_name', __('Agency')),
            new Event_Details_Item('agency_slug', __('Agency slug')),
            new Event_Details_Item('post_type', __('Post type')),
            new Event_Details_Item('post_ids', __('Post IDs to untag')),
            new Event_Details_Item('removed_ids', __('Post IDs untagged')),
            new Event_Details_Item('failed_ids', __('Post IDs that failed')),
            new Event_Details_Item('selected_count', __('Posts selected')),
            new Event_Details_Item('removed_count', __('Posts untagged')),
            new Event_Details_Item('skipped_only', __('Skipped: no other agency tag')),
            new Event_Details_Item('restored_count', __('Restored: lost its other agency tags during the run')),
            new Event_Details_Item('skipped_denied', __('Skipped: cannot edit')),
            new Event_Details_Item('skipped_unchanged', __('Skipped: not tagged with this agency')),
            new Event_Details_Item('failed_count', __('Failed')),
        ]);

        return $group;
    }
}
