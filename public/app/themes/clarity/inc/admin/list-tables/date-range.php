<?php

namespace MOJ_Intranet\List_Tables;

/**
 * A date range filter for post listings.
 *
 * Core offers a single month dropdown, which cannot express "everything before
 * 2020" or "2016 to 2019". This adds a pair of month selects that can, and
 * lets each editor choose which control they get from Screen Options, in the
 * same place and idiom as core's own "View mode" radios.
 *
 * The preference only sets the default. A link carrying m_after or m_before
 * shows the range control whatever the recipient has chosen, so a filtered
 * listing always explains itself.
 */

class Date_Range
{
    /**
     * User meta holding the chosen control.
     *
     * Stored as user meta, which is where core keeps its own Screen Options
     * values. Not as a user setting: those are read back from the
     * wp-settings-{id} cookie whenever the browser has one, and that cookie is
     * only refreshed later in the request than Screen Options saves, so a
     * stored value would stay invisible behind a stale cookie.
     *
     * Submitted as a plain field rather than through wp_screen_options, which
     * carries only one option per submission and is already spoken for by
     * "Number of items per page".
     */
    const SETTING = 'moj_date_filter';

    const MODE_MONTH = 'month';

    const MODE_RANGE = 'range';

    public function __construct()
    {
        // Saved on wp_loaded rather than load-edit.php: Screen Options posts
        // back to the listing, and set_screen_options() redirects and exits
        // (admin.php:116) long before load-edit.php (admin.php:390) would run.
        // Core only carries its own "mode" field across that redirect.
        add_action('wp_loaded', array($this, 'save_preference'));
        add_filter('screen_settings', array($this, 'render_screen_option'), 10, 2);

        // Keep core's month dropdown accurate while it is the chosen control.
        add_filter('pre_months_dropdown_query', array($this, 'filter_months_dropdown'), 10, 2);

        // Replace it with the range control when that is the chosen one.
        add_filter('disable_months_dropdown', array($this, 'disable_months_dropdown'), 10, 2);
        add_action('restrict_manage_posts', array($this, 'render_filter'), 10, 2);

        add_filter('parse_query', array($this, 'filter_posts_by_date_range'));
        add_action('admin_notices', array($this, 'render_inverted_range_notice'));
    }

    /**
     * Is this a post listing the date filter applies to?
     *
     * Limited to post types carrying the agency taxonomy, because the months
     * offered are drawn from the agency filtered listing.
     *
     * @param string $post_type
     * @return bool
     */
    protected function applies_to($post_type)
    {
        global $pagenow, $typenow;

        return $pagenow == 'edit.php'
            && $post_type == $typenow
            && is_object_in_taxonomy($post_type, 'agency');
    }

    /**
     * Which control to show: the stored preference, unless the request carries
     * range parameters, in which case the range control is needed to explain
     * the listing.
     *
     * @return string
     */
    protected function mode()
    {
        if ($this->requested_month('m_after') || $this->requested_month('m_before')) {
            return self::MODE_RANGE;
        }

        return $this->preference() == self::MODE_RANGE ? self::MODE_RANGE : self::MODE_MONTH;
    }

    /**
     * The stored preference, defaulting to core's single month dropdown.
     *
     * @return string
     */
    protected function preference()
    {
        $stored = get_user_meta(get_current_user_id(), self::SETTING, true);

        return $stored == self::MODE_RANGE ? self::MODE_RANGE : self::MODE_MONTH;
    }

    /**
     * Persist the preference submitted from Screen Options.
     *
     * @return void
     */
    public function save_preference()
    {
        global $pagenow;

        // Runs on wp_loaded, which is reached before auth_redirect(), so the
        // request is not necessarily authenticated yet.
        if (!get_current_user_id() || $pagenow != 'edit.php' || empty($_POST[self::SETTING])) {
            return;
        }

        // Submitted with the Screen Options form, so it carries that nonce.
        // Verified rather than asserted, so an unrelated post cannot change it.
        if (empty($_POST['screenoptionnonce']) ||
            !wp_verify_nonce($_POST['screenoptionnonce'], 'screen-options-nonce')
        ) {
            return;
        }

        $mode = $_POST[self::SETTING] == self::MODE_RANGE ? self::MODE_RANGE : self::MODE_MONTH;

        update_user_meta(get_current_user_id(), self::SETTING, $mode);

        // Screen Options returns to the listing that was on screen, which may
        // still carry the other control's parameters. Left alone they would go
        // on filtering, making the choice just made look as though it had no
        // effect. Only this request is affected.
        add_filter('wp_redirect', function ($location) use ($mode) {
            return remove_query_arg(
                $mode == self::MODE_RANGE ? array('m') : array('m_after', 'm_before'),
                $location
            );
        });
    }

    /**
     * Add the radios to the Screen Options tab.
     *
     * @param string $settings
     * @param \WP_Screen $screen
     * @return string
     */
    public function render_screen_option($settings, $screen)
    {
        if (!$this->applies_to($screen->post_type)) {
            return $settings;
        }

        // Screen Options only shows its submit button when something asks for it.
        add_filter('screen_options_show_submit', '__return_true');

        $mode = $this->preference();

        $options = array(
            self::MODE_MONTH => __('Single month'),
            self::MODE_RANGE => __('Date range'),
        );

        $fieldset = '<fieldset class="metabox-prefs"><legend>' . __('Filter by date') . '</legend>';

        foreach ($options as $value => $label) {
            $fieldset .= sprintf(
                '<label for="%1$s"><input id="%1$s" type="radio" name="%2$s" value="%3$s"%4$s /> %5$s</label>',
                esc_attr(self::SETTING . '-' . $value),
                esc_attr(self::SETTING),
                esc_attr($value),
                checked($mode, $value, false),
                esc_html($label)
            );
        }

        return $settings . $fieldset . '</fieldset>';
    }

    /**
     * Keep core's month dropdown to months the listing can show.
     *
     * WP_List_Table::months_dropdown() builds its list with a plain post_type
     * and post_status query, which knows nothing about the filters applied to
     * the listing, so it offers months that come back empty once selected.
     *
     * @param object[]|false $months
     * @param string $post_type
     * @return object[]|false
     */
    public function filter_months_dropdown($months, $post_type)
    {
        if (!$this->applies_to($post_type) || $this->mode() != self::MODE_MONTH) {
            return $months;
        }

        $context_months = Listing_Query::months($post_type);

        // Fall back to core's list rather than showing no dates at all.
        return empty($context_months) ? $months : $context_months;
    }

    /**
     * Remove core's month dropdown when the range control replaces it, so that
     * only one control governs dates.
     *
     * @param bool $disable
     * @param string $post_type
     * @return bool
     */
    public function disable_months_dropdown($disable, $post_type)
    {
        if (!$this->applies_to($post_type) || $this->mode() != self::MODE_RANGE) {
            return $disable;
        }

        return true;
    }

    /**
     * Render the range control.
     *
     * Both ends are optional, so "since March" and "before 2020" are each a
     * single selection. Selecting the same month at both ends filters to that
     * month. The script below keeps the two ends in a possible order.
     *
     * @param string $post_type
     * @param string $which
     * @return void
     */
    public function render_filter($post_type, $which)
    {
        if ($which != 'top' ||
            !$this->applies_to($post_type) ||
            $this->mode() != self::MODE_RANGE
        ) {
            return;
        }

        $months = $this->with_requested_months(Listing_Query::months($post_type));

        if (empty($months)) {
            return;
        }

        global $wp_locale;

        $selects = array(
            'm_after'  => array(__('From: earliest'), __('Filter by date, from')),
            'm_before' => array(__('To: latest'), __('Filter by date, to')),
        );

        foreach ($selects as $name => $labels) {
            $month_arg = $this->requested_month($name);
            $selected = $month_arg
                ? (int) sprintf('%04d%02d', $month_arg['year'], $month_arg['month'])
                : 0;

            printf(
                '<label for="%1$s" class="screen-reader-text">%2$s</label>
                <select name="%1$s" id="%1$s">
                    <option value="0"%3$s>%4$s</option>',
                esc_attr($name),
                esc_html($labels[1]),
                selected($selected, 0, false),
                esc_html($labels[0])
            );

            // Grouped by year: an agency with a long history can list well over
            // a hundred months, which is unusable as one flat run of options.
            $year = null;

            foreach ($months as $month) {
                if (!$month->year) {
                    continue;
                }

                if ($year !== $month->year) {
                    if ($year !== null) {
                        echo '</optgroup>';
                    }

                    printf('<optgroup label="%s">', esc_attr($month->year));
                    $year = $month->year;
                }

                $value = sprintf('%04d%02d', $month->year, $month->month);

                printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr($value),
                    selected($selected, (int) $value, false),
                    esc_html($wp_locale->get_month($month->month) . ' ' . $month->year)
                );
            }

            if ($year !== null) {
                echo '</optgroup>';
            }

            echo '</select>';
        }

        // Months that would invert the range are disabled rather than left to
        // produce an empty listing that reads as "nothing was published then".
        // Each end constrains the other, so it works whichever is chosen first.
        ?>
        <script>
        (function () {
            var after = document.getElementById('m_after'),
                before = document.getElementById('m_before'),
                OPEN = '0';

            if (!after || !before) {
                return;
            }

            // Values are YYYYMM of equal length, so they compare as strings.
            function disableImpossible() {
                var from = after.value,
                    to = before.value;

                Array.prototype.forEach.call(before.options, function (option) {
                    option.disabled = option.value !== OPEN && from !== OPEN && option.value < from;
                });

                Array.prototype.forEach.call(after.options, function (option) {
                    option.disabled = option.value !== OPEN && to !== OPEN && option.value > to;
                });
            }

            // A new selection can strand the other end behind it. Release that
            // end rather than leave a pair that cannot match anything.
            function sync(changed) {
                var from = after.value,
                    to = before.value;

                if (from !== OPEN && to !== OPEN && from > to) {
                    (changed === after ? before : after).value = OPEN;
                }

                disableImpossible();
            }

            after.addEventListener('change', function () {
                sync(after);
            });

            before.addEventListener('change', function () {
                sync(before);
            });

            disableImpossible();
        }());
        </script>
        <?php
    }

    /**
     * Explain a range that runs backwards.
     *
     * The script on the control stops the two ends being put in this order,
     * but a hand written or shared link can still carry them. Without this the
     * listing would just say "No posts found", which reads as nothing having
     * been published rather than as a range that cannot match.
     *
     * @return void
     */
    public function render_inverted_range_notice()
    {
        global $typenow, $wp_locale;

        if (!$this->applies_to($typenow)) {
            return;
        }

        $after = $this->requested_month('m_after');
        $before = $this->requested_month('m_before');

        if (!$after || !$before || !$this->is_inverted($after, $before)) {
            return;
        }

        $describe = function ($month) use ($wp_locale) {
            return $wp_locale->get_month($month['month']) . ' ' . $month['year'];
        };

        wp_admin_notice(
            sprintf(
                /* translators: 1: Start month and year, 2: End month and year. */
                esc_html__('No posts can match: the range starts in %1$s and ends in %2$s. Choose an end month that is not before the start month.'),
                '<strong>' . esc_html($describe($after)) . '</strong>',
                '<strong>' . esc_html($describe($before)) . '</strong>'
            ),
            array('type' => 'warning')
        );
    }

    /**
     * Does this range run backwards?
     *
     * @param array $after
     * @param array $before
     * @return bool
     */
    protected function is_inverted($after, $before)
    {
        return ($after['year'] * 100 + $after['month']) > ($before['year'] * 100 + $before['month']);
    }

    /**
     * Add any requested bound the listing itself cannot offer.
     *
     * The options are the months that hold posts, so a bound naming any other
     * month, from a shared link, a hand written URL, or a month that only has
     * posts under a different agency, would leave both selects showing their
     * open ended default while the listing was still filtered. The filter would
     * be in force with nothing on screen to say so, and no way to clear it.
     *
     * Such a bound is added to the list so that it shows as selected and can be
     * changed. It carries no posts, so it cannot make an empty selection
     * reachable from the control itself.
     *
     * @param object[] $months
     * @return object[]
     */
    protected function with_requested_months($months)
    {
        $known = array();

        foreach ($months as $month) {
            $known[sprintf('%04d%02d', $month->year, $month->month)] = true;
        }

        foreach (array('m_after', 'm_before') as $name) {
            $requested = $this->requested_month($name);

            if (!$requested) {
                continue;
            }

            $key = sprintf('%04d%02d', $requested['year'], $requested['month']);

            if (isset($known[$key])) {
                continue;
            }

            $months[] = (object) array(
                'year'  => $requested['year'],
                'month' => $requested['month'],
            );

            $known[$key] = true;
        }

        // Newest first, as the listing's own months already are.
        usort($months, function ($a, $b) {
            return ($b->year * 100 + $b->month) <=> ($a->year * 100 + $a->month);
        });

        return $months;
    }

    /**
     * Apply the range to the listing.
     *
     * Applied whenever the parameters are present, whatever the preference, so
     * that a shared link filters the same way for everyone.
     *
     * Only the listing itself: the counts above it, and the months offered
     * here, are worked out with queries of their own and, like core's,
     * describe the whole listing rather than the current filter.
     *
     * @param \WP_Query $query
     * @return mixed
     */
    public function filter_posts_by_date_range(\WP_Query $query)
    {
        global $typenow;

        if (!$this->applies_to($typenow) || !$query->is_main_query()) {
            return $query;
        }

        // Core's month parameter has no control on screen once the range
        // replaces the dropdown, so an m left over in the URL, from filtering
        // by month before switching, would filter the listing with nothing to
        // show for it. The range is the only date filter in this mode.
        if ($this->mode() == self::MODE_RANGE) {
            $query->query_vars['m'] = 0;
        }

        $after = $this->requested_month('m_after');
        $before = $this->requested_month('m_before');

        if (!$after && !$before) {
            return $query;
        }

        $date_query = array('inclusive' => true);

        if ($after) {
            $date_query['after'] = $after;
        }

        if ($before) {
            $date_query['before'] = $before;
        }

        $query->query_vars['date_query'] = array($date_query);

        return $query;
    }

    /**
     * Read and validate a YYYYMM request parameter.
     *
     * @param string $key
     * @return array|false Year and month, or false when absent or unusable.
     */
    protected function requested_month($key)
    {
        $value = $_GET[$key] ?? '';

        if (!is_scalar($value) || !preg_match('/^([0-9]{4})([0-9]{2})$/', (string) $value, $parts)) {
            return false;
        }

        $month = (int) $parts[2];

        if ($month < 1 || $month > 12) {
            return false;
        }

        return array(
            'year'  => (int) $parts[1],
            'month' => $month,
        );
    }
}
