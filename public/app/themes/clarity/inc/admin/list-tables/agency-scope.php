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

    public function __construct()
    {
        // After the date controls, which render at the default priority.
        add_action('restrict_manage_posts', array($this, 'render_filter'), 11, 2);
        add_filter('parse_query', array($this, 'filter_posts_by_scope'));

        // On admin_init, by which point the taxonomy knows its post types.
        add_action('admin_init', array($this, 'register_bulk_action'));
        add_action('admin_notices', array($this, 'render_result_notice'));
    }

    /**
     * Offer the bulk action on every listing the agency taxonomy applies to.
     *
     * @return void
     */
    public function register_bulk_action()
    {
        $taxonomy = get_taxonomy('agency');

        if (!$taxonomy) {
            return;
        }

        foreach ($taxonomy->object_type as $post_type) {
            if (!post_type_exists($post_type)) {
                continue;
            }

            add_filter('bulk_actions-edit-' . $post_type, array($this, 'add_bulk_action'));
            add_filter('handle_bulk_actions-edit-' . $post_type, array($this, 'handle_bulk_action'), 10, 3);
        }
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
     * page was open. Each post is checked again here, and any that would be
     * left with no agency is skipped and reported.
     *
     * @param string $sendback
     * @param string $doaction
     * @param int[] $post_ids
     * @return string
     */
    public function handle_bulk_action($sendback, $doaction, $post_ids)
    {
        if ($doaction !== self::BULK_ACTION) {
            return $sendback;
        }

        $context = Agency_Context::get_agency_context();
        $term = $context ? get_term_by('slug', $context, 'agency') : false;

        if (!$term) {
            return $sendback;
        }

        $selected = 0;
        $removed = 0;
        $only = 0;
        $denied = 0;
        $unchanged = 0;
        $failed = 0;

        foreach ((array) $post_ids as $post_id) {
            $post_id = (int) $post_id;
            $selected++;

            if (!current_user_can('edit_post', $post_id)) {
                $denied++;
                continue;
            }

            $terms = wp_get_object_terms($post_id, 'agency', array('fields' => 'ids'));

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

            $result = wp_remove_object_terms($post_id, $term->term_id, 'agency');

            if (true !== $result) {
                // Counted separately: a failure is neither a removal nor a post
                // that was left alone, and the totals have to keep adding up.
                $failed++;
                continue;
            }

            $removed++;
        }

        return add_query_arg(
            array(
                'agency_selected'  => $selected,
                'agency_removed'   => $removed,
                'agency_only'      => $only,
                'agency_denied'    => $denied,
                'agency_unchanged' => $unchanged,
                'agency_failed'    => $failed,
            ),
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

        $messages = array(
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
        );

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
            array('type' => ($only || $denied || $unchanged || $failed) ? 'warning' : 'success')
        );
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

        return $pagenow == 'edit.php'
            && $post_type == $typenow
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

        return in_array($scope, array(self::SCOPE_ONLY, self::SCOPE_SHARED), true) ? $scope : false;
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
            return array();
        }

        $slugs = get_terms(array(
            'taxonomy'   => 'agency',
            'hide_empty' => false,
            'fields'     => 'slugs',
        ));

        if (is_wp_error($slugs)) {
            return array();
        }

        return array_values(array_diff($slugs, array($context)));
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
        if ($which != 'top' || !$this->applies_to($post_type)) {
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
        $options = array(
            /* translators: %s: Agency name. */
            ''                 => sprintf(__('Tagged %s'), $name),
            /* translators: %s: Agency name. */
            self::SCOPE_ONLY   => sprintf(__('Tagged %s only'), $name),
            /* translators: %s: Agency name. */
            self::SCOPE_SHARED => sprintf(__('Tagged %s and others'), $name),
        );

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

        if (!$this->applies_to($typenow) || !$query->is_main_query()) {
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
        $tax_query = $query->query_vars['tax_query'] ?? array();

        $tax_query[] = array(
            'taxonomy'         => 'agency',
            'field'            => 'slug',
            'terms'            => $others,
            'operator'         => $scope == self::SCOPE_ONLY ? 'NOT IN' : 'IN',
            'include_children' => false,
        );

        $query->query_vars['tax_query'] = $tax_query;

        return $query;
    }
}
