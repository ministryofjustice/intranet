<?php

namespace MOJ_Intranet\List_Tables;

use Agency_Context;

/**
 * A filter for how exclusively a post belongs to the current agency.
 *
 * Post listings already show everything tagged with the agency in context,
 * whether or not the post also belongs to others. That total hides a division
 * that matters: a post shared with another agency still has an owner if this
 * agency lets go of it, while a post tagged with this agency alone does not.
 *
 * Untagging the second kind leaves a post with no agency at all, which removes
 * it from every admin listing while it stays published, so the two are worth
 * being able to look at separately before acting on them in bulk.
 */

class Agency_Scope
{
    const PARAM = 'agency_scope';

    const SCOPE_ONLY = 'only';

    const SCOPE_SHARED = 'shared';

    const BULK_ACTION = 'remove_agency';

    // Limit the bulk action to specific post types.
    const BULK_ACTION_POST_TYPES = ['event', 'news', 'post'];

    public function __construct()
    {
        // After the date controls, which render at the default priority.
        add_action('restrict_manage_posts', [$this, 'render_filter'], 11, 2);
        add_filter('parse_query', [$this, 'filter_posts_by_scope']);

        // On admin_init, by which point the taxonomy knows its post types.
        add_action('admin_init', [$this, 'register_bulk_action']);
        add_action('admin_notices', [$this, 'render_result_notice']);
    }

    /**
     * Offer the bulk action on specified post types.
     *
     * @return void
     */
    public function register_bulk_action()
    {
        foreach (self::BULK_ACTION_POST_TYPES as $post_type) {
            if (!in_array($post_type, self::BULK_ACTION_POST_TYPES, true)) {
                continue;
            }

            add_filter('bulk_actions-edit-' . $post_type, [$this, 'add_bulk_action']);
            add_filter('bulk_actions-edit-' . $post_type, [$this, 'restrict_trash_action']);
            add_filter('handle_bulk_actions-edit-' . $post_type, [$this, 'handle_bulk_action'], 10, 3);
        }
    }

    /**
     * Offer "Move to Bin" outside HQ only while the listing is showing posts
     * tagged with this agency alone.
     *
     * Binning a post removes it for every agency it is tagged with, so an
     * agency other than HQ may only bin the posts that are its own alone.
     *
     * @param array $actions
     * @return array
     */
    public function restrict_trash_action($actions)
    {
        $screen = get_current_screen();

        if (!$screen ||
            !$this->applies_to($screen->post_type) ||
            Agency_Context::get_agency_context() === 'hq' ||
            $this->requested_scope() === self::SCOPE_ONLY
        ) {
            return $actions;
        }

        unset($actions['trash']);

        return $actions;
    }

    /**
     * Offer to remove the agency, but only while the listing is showing posts
     * shared with another one.
     *
     * Every row in that view visibly belongs somewhere else as well, so the
     * action cannot leave a post with no agency at all. Outside it the same
     * selection could include posts this agency holds alone, which is why the
     * action is not offered there.
     *
     * @param array $actions
     * @return array
     */
    public function add_bulk_action($actions)
    {
        $screen = get_current_screen();

        if (!$screen ||
            !$this->applies_to($screen->post_type) ||
            !$this->current_user_can_remove_tag() ||
            $this->requested_scope() !== self::SCOPE_SHARED
        ) {
            return $actions;
        }

        $context = Agency_Context::get_agency_context();
        $term = $context ? get_term_by('slug', $context, 'agency') : false;

        if (!$term) {
            return $actions;
        }

        // "tag" rather than "remove CICA", which sitting beside "Move to Bin"
        // could be read as deleting the agency itself. The front end already
        // calls these tags: "Content tagged as: CICA, HMCTS...".
        /* translators: %s: Agency name. */
        $actions[self::BULK_ACTION] = sprintf(__('Remove %s tag'), $term->name);

        return $actions;
    }

    /**
     * Remove the current agency from the selected posts.
     *
     * The listing has already been narrowed to shared posts, but a selection
     * can go stale: another editor may have removed the other agency while this
     * page was open. Each post is checked again here, and again after the
     * removal in case the last other tag went in between, so that a post is
     * never left without an agency. Anything skipped is reported.
     *
     * The work is done in two passes, sorting first and changing second, so
     * that the posts about to lose the tag are known, and announced, before
     * the first of them is touched. Term removals are not transactional: a
     * request that dies part way through leaves the posts already handled
     * untagged. Announcing the list ahead of the changes lets an audit log
     * name every post that might have been altered even then.
     *
     * @param string $sendback
     * @param string $doaction
     * @param int[] $post_ids
     * @return string
     */
    public function handle_bulk_action($sendback, $doaction, $post_ids)
    {
        global $typenow;

        // The capability is checked again here, not only when the action is
        // offered, since a request can be made without the dropdown.
        if ($doaction !== self::BULK_ACTION || !$this->current_user_can_remove_tag()) {
            return $sendback;
        }

        $context = Agency_Context::get_agency_context();
        $term = $context ? get_term_by('slug', $context, 'agency') : false;

        if (!$term) {
            return $sendback;
        }

        $selected = 0;
        $only = 0;
        $denied = 0;
        $unchanged = 0;
        $eligible_ids = [];

        // First pass: sort the selection without changing anything.
        foreach ((array) $post_ids as $post_id) {
            $post_id = (int) $post_id;
            $selected++;

            if (!current_user_can('edit_post', $post_id)) {
                $denied++;
                continue;
            }

            $terms = wp_get_object_terms($post_id, 'agency', ['fields' => 'ids']);

            if (is_wp_error($terms) || !in_array($term->term_id, $terms)) {
                // Counted, not passed over in silence: every selected post has
                // to be accounted for, or the totals cannot be reconciled with
                // what was on screen.
                $unchanged++;
                continue;
            }

            if (count($terms) < 2) {
                // Removing this would leave the post with no agency, which
                // hides it from every listing while it stays published.
                $only++;
                continue;
            }

            $eligible_ids[] = $post_id;
        }

        $removed_ids = [];
        $failed_ids = [];

        if (!empty($eligible_ids)) {
            $details = [
                'term_id'     => (int) $term->term_id,
                'agency_slug' => $term->slug,
                'agency_name' => $term->name,
                'post_type'   => $typenow,
                'selected'    => $selected,
                'denied'      => $denied,
                'unchanged'   => $unchanged,
            ];

            /**
             * Fires before the first tag is removed.
             *
             * Listeners that record the change should do so here rather than
             * waiting for the finished action, which will not fire if the
             * request dies during the removals.
             *
             * @param array $details {
             *     @type int    $term_id     The agency term being removed.
             *     @type string $agency_slug
             *     @type string $agency_name
             *     @type string $post_type   The post type of the listing.
             *     @type int[]  $post_ids    Every post about to lose the tag.
             *     @type int    $selected    How many posts were selected.
             *     @type int    $only        Skipped: no other agency tag.
             *     @type int    $denied      Skipped: user cannot edit.
             *     @type int    $unchanged   Skipped: not tagged with this agency.
             * }
             */
            do_action('clarity/agency_scope/agency_tag_removal_started', $details + [
                'post_ids' => $eligible_ids,
                'only'     => $only,
            ]);

            // Second pass: the changes.
            foreach ($eligible_ids as $post_id) {
                $result = wp_remove_object_terms($post_id, $term->term_id, 'agency');

                if (true !== $result) {
                    // Counted separately: a failure is neither a removal nor a
                    // post that was left alone, and the totals have to keep
                    // adding up.
                    $failed_ids[] = $post_id;
                    continue;
                }

                // The sorting above and the removal are two steps, so someone
                // working in another agency context can take the last remaining
                // tag in between and leave the post with none. Reading back
                // afterwards and restoring the tag holds the guarantee this
                // action rests on: it never leaves a post without an agency.
                // Appending rather than setting, so a tag added in the meantime
                // is not overwritten.
                $remaining = wp_get_object_terms($post_id, 'agency', ['fields' => 'ids']);

                if (is_wp_error($remaining) || empty($remaining)) {
                    // Put the tag back whether the post was found with none or
                    // the read failed: an unknown state is treated as the
                    // worst one, so the guarantee holds either way.
                    $restored = wp_set_object_terms($post_id, [$term->term_id], 'agency', true);

                    if (is_wp_error($restored) || is_wp_error($remaining)) {
                        // A failed restore may have left the post with no
                        // agency, and a failed read means nothing is known.
                        // Both are reported as failures to be retried, and
                        // reach the audit log through the finished action.
                        $failed_ids[] = $post_id;
                        continue;
                    }

                    $only++;
                    continue;
                }

                $removed_ids[] = $post_id;
            }

            /**
             * Fires after the removals, with the outcome.
             *
             * Same keys as the started action, where $post_ids now holds only
             * the posts that actually lost the tag, plus:
             *
             * @param array $details {
             *     @type int[] $failed_ids Posts the removal failed on.
             *     @type int   $removed    How many posts lost the tag.
             *     @type int   $failed     How many removals failed.
             * }
             */
            do_action('clarity/agency_scope/agency_tag_removal_finished', $details + [
                'post_ids'   => $removed_ids,
                'failed_ids' => $failed_ids,
                'only'       => $only,
                'removed'    => count($removed_ids),
                'failed'     => count($failed_ids),
            ]);
        }

        return add_query_arg(
            [
                'agency_selected'  => $selected,
                'agency_removed'   => count($removed_ids),
                'agency_only'      => $only,
                'agency_denied'    => $denied,
                'agency_unchanged' => $unchanged,
                'agency_failed'    => count($failed_ids),
            ],
            $sendback
        );
    }

    /**
     * Report what the bulk action did.
     *
     * @return void
     */
    public function render_result_notice()
    {
        global $typenow;

        if (!isset($_GET['agency_removed']) || !$this->applies_to($typenow)) {
            return;
        }

        $removed = (int) $_GET['agency_removed'];
        $selected = (int) ($_GET['agency_selected'] ?? $removed);
        $only = (int) ($_GET['agency_only'] ?? 0);
        $denied = (int) ($_GET['agency_denied'] ?? 0);
        $unchanged = (int) ($_GET['agency_unchanged'] ?? 0);
        $failed = (int) ($_GET['agency_failed'] ?? 0);

        $context = Agency_Context::get_agency_context();
        $term = $context ? get_term_by('slug', $context, 'agency') : false;
        $name = $term ? $term->name : $context;

        $messages = [
            sprintf(
                /* translators: 1: Agency name, 2: Number of posts, 3: Number selected. */
                _n(
                    '%1$s tag removed from %2$s of %3$s selected posts.',
                    '%1$s tag removed from %2$s of %3$s selected posts.',
                    $selected
                ),
                esc_html($name),
                number_format_i18n($removed),
                number_format_i18n($selected)
            ),
        ];

        if ($unchanged) {
            $messages[] = sprintf(
                /* translators: 1: Number of posts, 2: Agency name. */
                _n(
                    '%1$s post was left alone: it was not tagged %2$s.',
                    '%1$s posts were left alone: they were not tagged %2$s.',
                    $unchanged
                ),
                number_format_i18n($unchanged),
                esc_html($name)
            );
        }

        if ($only) {
            $messages[] = sprintf(
                /* translators: %s: Number of posts. */
                _n(
                    '%s post skipped: it has no other agency tag.',
                    '%s posts skipped: they have no other agency tag.',
                    $only
                ),
                number_format_i18n($only)
            );
        }

        if ($denied) {
            $messages[] = sprintf(
                /* translators: %s: Number of posts. */
                _n('%s post skipped: you cannot edit it.', '%s posts skipped: you cannot edit them.', $denied),
                number_format_i18n($denied)
            );
        }

        if ($failed) {
            $messages[] = sprintf(
                /* translators: %s: Number of posts. */
                _n(
                    '%s post could not be changed. Please try it again.',
                    '%s posts could not be changed. Please try them again.',
                    $failed
                ),
                number_format_i18n($failed)
            );
        }

        wp_admin_notice(
            implode(' ', $messages),
            ['type' => ($only || $denied || $unchanged || $failed) ? 'warning' : 'success']
        );
    }

    /**
     * May the current user take an agency tag off posts?
     *
     * This is the bulk form of the "Opt-out" quick action, which
     * Taxonomies\Agency offers only to users with opt_in_content. Having an
     * agency context is not enough on its own: regional editors have one
     * without that capability.
     *
     * @return bool
     */
    protected function current_user_can_remove_tag()
    {
        return current_user_can('opt_in_content');
    }

    /**
     * Is this a post listing the filter applies to?
     *
     * @param string $post_type
     * @return bool
     */
    protected function applies_to($post_type)
    {
        global $pagenow, $typenow;

        return $pagenow === 'edit.php'
            && $post_type === $typenow
            && is_object_in_taxonomy($post_type, 'agency')
            && Agency_Context::current_user_can_have_context();
    }

    /**
     * The requested scope, or false when the listing is unrestricted.
     *
     * @return string|false
     */
    protected function requested_scope()
    {
        $scope = $_GET[self::PARAM] ?? '';

        if (!is_scalar($scope)) {
            return false;
        }

        return in_array($scope, [self::SCOPE_ONLY, self::SCOPE_SHARED], true) ? $scope : false;
    }

    /**
     * The agencies a post could be shared with: every one but the current.
     *
     * @return string[]
     */
    protected function other_agencies()
    {
        $context = Agency_Context::get_agency_context();

        if (empty($context)) {
            return [];
        }

        $slugs = get_terms([
            'taxonomy'   => 'agency',
            'hide_empty' => false,
            'fields'     => 'slugs',
        ]);

        if (is_wp_error($slugs)) {
            return [];
        }

        return array_values(array_diff($slugs, [$context]));
    }

    /**
     * Render the filter, named after the agency in context so that it cannot be
     * read as a filter across agencies.
     *
     * @param string $post_type
     * @param string $which
     * @return void
     */
    public function render_filter($post_type, $which)
    {
        if ($which !== 'top' || !$this->applies_to($post_type)) {
            return;
        }

        $context = Agency_Context::get_agency_context();

        if (empty($context) || empty($this->other_agencies())) {
            // Nothing to be shared with, so the division does not exist.
            return;
        }

        $term = get_term_by('slug', $context, 'agency');
        $name = $term ? $term->name : $context;

        // "Tagged", matching the front end's "Content tagged as: CICA, HMCTS..."
        // and the bulk action that removes the tag.
        $options = [
            /* translators: %s: Agency name. */
            ''                 => sprintf(__('Tagged %s'), $name),
            /* translators: %s: Agency name. */
            self::SCOPE_ONLY   => sprintf(__('Tagged %s only'), $name),
            /* translators: %s: Agency name. */
            self::SCOPE_SHARED => sprintf(__('Tagged %s and others'), $name),
        ];

        $selected = (string) $this->requested_scope();

        printf(
            '<label for="%1$s" class="screen-reader-text">%2$s</label>
            <select name="%1$s" id="%1$s">',
            esc_attr(self::PARAM),
            esc_html__('Filter by agency')
        );

        foreach ($options as $value => $label) {
            // The selected state is emitted as a literal rather than by passing
            // the requested value through to the output. requested_scope()
            // already restricts it to two known values, but keeping request
            // data out of the markup altogether makes that plain to a reader
            // and to static analysis.
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($value),
                $value === $selected ? ' selected="selected"' : '',
                esc_html($label)
            );
        }

        echo '</select>';
    }

    /**
     * Narrow the listing to posts held only by this agency, or only to those
     * shared with another.
     *
     * Applied to the listing alone. The counts above it, like core's, describe
     * the whole listing rather than the filter currently in force.
     *
     * @param \WP_Query $query
     * @return mixed
     */
    public function filter_posts_by_scope(\WP_Query $query)
    {
        global $typenow;

        // The listing itself, and the query that finds the months the date
        // controls can offer: those have to agree with the listing or the
        // controls offer months that come back empty. The status counts are
        // deliberately left out, see the docblock.
        $scoped = $query->is_main_query() || $query->get(Listing_Query::MONTHS_QUERY_VAR);

        if (!$this->applies_to($typenow) || !$scoped) {
            return $query;
        }

        $scope = $this->requested_scope();
        $others = $this->other_agencies();

        if (!$scope || empty($others)) {
            return $query;
        }

        // filter_posts_by_agency() has already limited the listing to the
        // current agency, so carrying one of the others is what separates a
        // shared post from one held alone.
        $tax_query = $query->query_vars['tax_query'] ?? [];

        $tax_query[] = [
            'taxonomy'         => 'agency',
            'field'            => 'slug',
            'terms'            => $others,
            'operator'         => $scope === self::SCOPE_ONLY ? 'NOT IN' : 'IN',
            'include_children' => false,
        ];

        $query->query_vars['tax_query'] = $tax_query;

        return $query;
    }
}
