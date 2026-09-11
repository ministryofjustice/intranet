<?php

namespace MOJ_Intranet\Taxonomies;

use Agency_Context;
use Agency_Editor;
use MOJ_Intranet\List_Tables\Listing_Query;

class Agency extends Taxonomy
{
    protected $name = 'agency';

    protected $object_types = array(
        'user',
        'news',
        'post',
        'page',
        'webchat',
        'event',
        'document',
        'regional_news',
        'regional_page',
        'condolences',
        'note-from-jo',
        'note-from-amy',
        'note-from-antonia',
    );

    /**
     * The post types the taxonomy applies to: $object_types without users.
     * Populated on construct.
     *
     * @var string[]
     */
    protected $post_types = [];

    /**
     * Memo to keep track of post counts over a single request. Prevents
     * duplicate slow database queries, and stops filter_post_counts()
     * recursing if its inner query triggers wp_count_posts() again.
     *
     * @var array
     */
    protected $counts_memo = [];

    protected $args = array(
        'labels' => array(
            'name' => 'Agencies',
            'singular_name' => 'Agency',
            'menu_name' => 'Agencies',
            'all_items' => 'All Agencies',
            'parent_item' => 'Parent Agency',
            'parent_item_colon' => 'Parent Agency:',
            'new_item_name' => 'New Agency Name',
            'add_new_item' => 'Add New Agency',
            'edit_item' => 'Edit Agency',
            'update_item' => 'Update Agency',
            'separate_items_with_commas' => 'Separate Agencies with commas',
            'search_items' => 'Search Agencies',
            'add_or_remove_items' => 'Add or remove Agencies',
            'choose_from_most_used' => 'Choose from the most used Agencies',
            'not_found' => 'Not Found',
        ),
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'show_tagcloud' => false,
        'rewrite' => false,
        'capabilities' => array(
            'manage_terms' => 'manage_agencies',
            'edit_terms' => 'manage_agencies',
            'delete_terms' => 'manage_agencies',
            'assign_terms' => 'assign_agencies_to_posts',
        ),
        'query_var' => 'agency_filter'
    );

    public function __construct()
    {
        parent::__construct();

        $this->post_types = array_values(array_diff($this->object_types, ['user']));

        if (current_user_can('manage_agencies')) {
            add_action('admin_menu', array($this, 'add_admin_menu_item'));
        }

        if (current_user_can('assign_agencies_to_posts')) {
            // Show form fields to edit user agency
            // Using priority 9 here to bump it above "More fields" section
            add_action('show_user_profile', array($this, 'edit_user_profile'), 9);
            add_action('edit_user_profile', array($this, 'edit_user_profile'), 9);
            add_action('user_new_form', array($this, 'edit_user_profile'), 9);

            // Update the agency terms when the edit user page is updated
            add_action('personal_options_update', array($this, 'edit_user_profile_save'), 9);
            add_action('edit_user_profile_update', array($this, 'edit_user_profile_save'), 9);
            add_action('user_register', array($this, 'edit_user_profile_save'), 9);
        }

        // Add page agency meta box
        if (! current_user_can('manage_agencies')) {
            // Remove agency meta box
            add_action('admin_menu', array($this, 'remove_agency_meta_box'));
        }

        if (Agency_Context::current_user_can_have_context()) {
            // Post filtering
            add_filter('parse_query', array($this, 'filter_posts_by_agency'));

            // Make the post counts above admin listings agency aware.
            // 1. All, Published, Drafts, Bin etc. come from wp_count_posts().
            add_filter('wp_count_posts', [$this, 'filter_post_counts'], 10, 3);

            // 2. "Mine" has no filter of its own, so its rendered link is amended.
            foreach ($this->post_types as $post_type) {
                add_filter('views_edit-' . $post_type, [$this, 'filter_status_views'], 11);
            }

            // Auto-tag agency
            add_action('save_post', array($this, 'set_agency_terms_on_save_post'));

            // Capabilities
            if (! current_user_can('manage_agencies')) {
                add_action('map_meta_cap', array($this, 'restrict_edit_post_to_current_agency'), 10, 4);
            }


            if (current_user_can('opt_in_content')) {
                // Quick actions
                add_action('page_row_actions', array($this, 'add_opt_in_out_quick_actions'), 10, 2);
                add_action('post_row_actions', array($this, 'add_opt_in_out_quick_actions'), 10, 2);
                add_action('load-post.php', array($this, 'quick_action_opt_in_out'));
            }
        }

        add_action('map_meta_cap', array($this, 'restrict_archived_content_to_agency'), 10, 2);
    }

    public function add_admin_menu_item()
    {
        add_submenu_page('users.php', 'Agencies', 'Agencies', 'administrator', 'edit-tags.php?taxonomy=agency&post_type=user');
    }

    /**
     * Adds an additional settings section on the edit user/profile page in the admin.  This section allows users to
     * select a profession from a checkbox of terms from the profession taxonomy.  This is just one example of
     * many ways this can be handled.
     *
     * @param object $user The user object currently being edited.
     */
    public function edit_user_profile($user)
    {
        $terms = get_terms($this->name, array(
            'hide_empty' => false,
        ));

        if (is_string($user) &&
            in_array($user, array('add-existing-user', 'add-new-user'))
        ) {
            $user = false;
        }

        // False = a new user is being setup with no ID yet
        if ($user !== false) {

            /**
             * If it is your own profile you're editing, make sure the ratio box reflects your agency.
            * This is for legacy situations as editors used to have multiple checkboxes selected.
            */

            $userProfileBeingEditedID = $user->ID;
            $currentEditorID = get_current_user_id();

            if ($userProfileBeingEditedID === $currentEditorID) {
                $context = Agency_Context::get_agency_context();
                wp_set_object_terms($user->ID, $context, 'agency', false);
                clean_object_term_cache($user->ID, 'agency');
            }
        };

        ?>

        <h3><?php _e('Agencies'); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="agency"><?php _e('Set your default agency'); ?></label></th>
                <td>
                    <p class="description">Determines which agency posts you're able to view and edit by default when you log in.</p>

                    <?php

                    // If there are any agency terms, loop through them and display checkboxes.
                    if (!empty($terms)) {
                        foreach ($terms as $term) { ?>
                    <input type="radio" name="agency[]" id="agency-<?php echo esc_attr($term->slug); ?>"
                        value="<?php echo esc_attr($term->slug); ?>"
                            <?php $user && checked(true, is_object_in_term($user->ID, 'agency', $term->slug)); ?> />
                    <label for="agency-<?php echo esc_attr($term->slug); ?>"><?php echo $term->name; ?></label>
                    <br />
                        <?php }
                    } /* If there are no agency terms, display a message. */
                    else {
                        _e('There are no agencies to choose from.');
                    }

                    ?>
                </td>
            </tr>

        </table>

        <?php
    }

    /**
     * Saves the term selected on the edit user/profile page in the admin. This function is triggered when the page
     * is updated.  We just grab the posted data and use wp_set_object_terms() to save it.
     *
     * @param int $user_id The ID of the user to save the terms for.
     */
    public function edit_user_profile_save(int $user_id): void
    {
        // Get the chosen agency value selected from the radio button
        $selectedAgency = $_POST['agency'] ?? Agency_Context::get_agency_context();

        // if selected, result is array, otherwise it's a string
        if (is_array($selectedAgency)) {
            $selectedAgency = array_shift($selectedAgency);
        }

        // Sanitize POST value and select chosen agency from array
        $newAgency = sanitize_text_field($selectedAgency);

        // Update the user's agency context
        update_user_meta($user_id, 'agency_context', $newAgency);

        // Set the terms for the user so their choice stays chosen in radio box
        wp_set_object_terms($user_id, $newAgency, 'agency');
        clean_object_term_cache($user_id, 'agency');
    }

    /**
     * Is an agency post list (edit.php) on screen?
     *
     * Only true for post types the agency taxonomy is registered on.
     *
     * @param string|null $post_type Also require the list to be for this post type.
     * @return bool
     */
    protected function is_agency_post_list_screen($post_type = null)
    {
        global $pagenow, $typenow;

        if ($pagenow !== 'edit.php' || !in_array($typenow, $this->post_types, true)) {
            return false;
        }

        return $post_type === null || $post_type === $typenow;
    }

    /**
     * Make the post status counts above the listing agency aware.
     *
     * wp_count_posts() counts every post of the type, so the "All", "Published",
     * "Drafts" etc. totals describe the whole site while the listing beneath them
     * is restricted by filter_posts_by_agency() and, for some post types, by
     * Region::filter_posts_by_region().
     *
     * Only the listing on screen is corrected. Other callers of wp_count_posts()
     * during the same request, such as the At a Glance dashboard widget or a
     * plugin asking about a different post type, keep their site wide answers.
     *
     * @param object $counts
     * @param string $post_type
     * @param string $perm
     * @return object
     */
    public function filter_post_counts($counts, $post_type, $perm)
    {
        global $wpdb;

        if (!$this->is_agency_post_list_screen($post_type)) {
            return $counts;
        }

        $memo_key = $post_type . '|' . $perm;

        if (isset($this->counts_memo[$memo_key])) {
            return $this->counts_memo[$memo_key];
        }

        // Seeded before the inner query runs. That query fires parse_query,
        // posts_clauses and the_posts, and a callback on any of them that calls
        // wp_count_posts() for this same post type would otherwise re-enter here
        // and recurse until the request runs out of stack.
        $this->counts_memo[$memo_key] = $counts;

        $request = Listing_Query::request(
            $post_type,
            "{$wpdb->posts}.ID, {$wpdb->posts}.post_status",
            [
                // Every status, so the Bin link keeps its count.
                'post_status' => get_post_stati(),
                // An empty $perm is ignored by WP_Query, same as omitting it.
                'perm'        => $perm,
            ]
        );

        if (empty($request)) {
            return $counts;
        }

        // $request is already prepared, so it is not passed through prepare().
        $results = $wpdb->get_results(
            "SELECT post_status, COUNT(DISTINCT ID) AS num_posts FROM ($request) AS counted GROUP BY post_status",
            ARRAY_A
        );

        if ($wpdb->last_error) {
            return $counts;
        }

        $context_counts = [];

        foreach ((array) $results as $row) {
            $context_counts[$row['post_status']] = (int) $row['num_posts'];
        }

        // Callers expect every registered status to be present.
        $this->counts_memo[$memo_key] = (object) array_merge(
            array_fill_keys(get_post_stati(), 0),
            $context_counts
        );

        return $this->counts_memo[$memo_key];
    }

    /**
     * Make the "Mine" count above the listing agency aware.
     *
     * This one cannot be corrected through filter_post_counts(): the count comes
     * from WP_Posts_List_Table::$user_posts_count, a private property set by its
     * own query, so the rendered link is amended here instead.
     *
     * Core only renders the link when that private count differs from the total
     * above it. The total is now agency scoped while the private count is not,
     * so the two can coincide and core drops the link even though the user does
     * have posts here. The entry is therefore rebuilt when it is missing.
     *
     * Runs late so that a link added by another plugin during load-edit.php is
     * already in $views and can be given its count.
     *
     * @param string[] $views
     * @return string[]
     */
    public function filter_status_views($views)
    {
        global $wpdb;

        $screen = get_current_screen();

        if (!$screen || !$this->is_agency_post_list_screen($screen->post_type)) {
            return $views;
        }

        // Counted through WP_Query so the listing's own filtering is respected,
        // including any rewriting of author queries. Counting post_author
        // directly overstates the total, which is the flaw in core's count.
        //
        // Counted with COUNT() rather than WP_Query's found_posts, because the
        // SQL_CALC_FOUND_ROWS that found_posts relies on takes seconds over the
        // joins an author query can accumulate.
        $request = Listing_Query::request(
            $screen->post_type,
            "{$wpdb->posts}.ID",
            [
                'post_status' => 'any',
                'author'      => get_current_user_id(),
            ]
        );

        if (empty($request)) {
            return $views;
        }

        // $request is already prepared, so it is not passed through prepare().
        $count = (int) $wpdb->get_var("SELECT COUNT(DISTINCT ID) FROM ($request) AS counted");

        if ($wpdb->last_error) {
            return $views;
        }

        // Nothing worth linking to, and core did not render the link either.
        if (!isset($views['mine']) && !$count) {
            return $views;
        }

        $count_html = '<span class="count">(' . number_format_i18n($count) . ')</span>';

        // The common case: core rendered the link, so only the number changes.
        // Leaves the link untouched if core's markup ever changes.
        if (isset($views['mine']) && preg_match('/<span class="count">\([^)]*\)<\/span>/', $views['mine'])) {
            $views['mine'] = preg_replace(
                '/<span class="count">\([^)]*\)<\/span>/',
                $count_html,
                $views['mine'],
                1
            );

            return $views;
        }

        // Either core dropped the link, or something else supplied one with no
        // count. Build the entry so a number is always shown, matching the
        // markup of WP_List_Table::get_views_links().
        $user_id = get_current_user_id();

        $url = add_query_arg(
            [
                'post_type' => $screen->post_type,
                'author'    => $user_id,
            ],
            'edit.php'
        );

        $is_current = isset($_GET['author']) && $user_id === (int) $_GET['author'];

        $mine = sprintf(
            '<a href="%s"%s>%s</a>',
            esc_url($url),
            $is_current ? ' class="current" aria-current="page"' : '',
            sprintf(
                /* translators: %s: Number of posts. */
                _nx(
                    'Mine <span class="count">(%s)</span>',
                    'Mine <span class="count">(%s)</span>',
                    $count,
                    'posts'
                ),
                number_format_i18n($count)
            )
        );

        // Core places "Mine" directly after "All".
        $rebuilt = [];

        foreach ($views as $key => $view) {
            if ($key === 'mine') {
                // Dropped here and re-added in core's position below.
                continue;
            }

            $rebuilt[$key] = $view;

            if ($key === 'all') {
                $rebuilt['mine'] = $mine;
            }
        }

        if (!isset($rebuilt['mine'])) {
            $rebuilt['mine'] = $mine;
        }

        return $rebuilt;
    }

    /**
     * Add taxonomy filter to the WP_Query object used for displaying posts
     * on the page.
     *
     * @param \WP_Query $query
     * @return mixed
     */
    public function filter_posts_by_agency(\WP_Query $query)
    {
        if (!$this->is_agency_post_list_screen()) {
            return $query;
        }

        // Define the agency taxonomy filter
        $agency = array(Agency_Context::get_agency_context());

        // Show HQ posts?
        if (isset($_GET['show-hq-posts']) && $_GET['show-hq-posts'] == '1') {
            $agency[] = 'hq';
        }

        $query->query_vars['agency_filter'] = $agency;

        return $query;
    }

    /**
     * On save of post set the agency of the content to the current agency context
     * @param int $post_id
     */
    public function set_agency_terms_on_save_post($post_id)
    {
        $post_type = get_post_type($post_id);
        if (!in_array($post_type, $this->post_types, true) ||
            !Agency_Context::current_user_can_have_context()
        ) {
            return;
        }

        $terms = wp_get_object_terms($post_id, 'agency');

        if (empty($terms)) {
            $agency_context = Agency_Context::get_agency_context();
            wp_set_object_terms($post_id, $agency_context, 'agency');
        }
    }

    /**
     * Remove agency meta box from post edit pages.
     */
    public function remove_agency_meta_box()
    {
        foreach ($this->post_types as $post_type) {
            remove_meta_box('agencydiv', $post_type, 'normal');
        }
    }


    /**
     * Stop users from editing posts that belong to agencies which are not
     * the current agency context.
     *
     * @param $caps
     * @param $cap
     * @param $user_id
     * @param $args
     *
     * @return array
     */
    public function restrict_edit_post_to_current_agency($caps, $cap, $user_id, $args)
    {
        $filter_caps = [
            'edit_post',
            'delete_post',
            'edit_news',
            'delete_news',
            'edit_notes_from_jo',
            'delete_notes_from_jo',
            'edit_notes_from_amy',
            'delete_notes_from_amy',
            'edit_notes_from_antonia',
            'delete_notes_from_antonia'
        ];

        if (!in_array($cap, $filter_caps) || !isset($args[0])) {
            // Not relevant, return early.
            return $caps;
        }
        $post_id = $args[0];
        $post_type = get_post_type($post_id);
        if (!in_array($post_type, $this->post_types, true)) {
            // Not relevant, return early.
            return $caps;
        }
        $owner = Agency_Editor::get_post_agency($post_id);
        $context = Agency_Context::get_agency_context();
        if ($owner !== $context) {
            // User does not have permission to edit this post
            $caps[] = 'do_not_allow';
        }
        return $caps;
    }

    /**
     * Filter the `archived_content` capability so that only users with
     * the correct agency context can view archived content.
     * 
     * Currently, only users in the 'hq' agency context can view archived content.
     *
     * @param $caps
     * @param $cap
     *
     * @return array
     */
    public function restrict_archived_content_to_agency($caps, $cap)
    {
        if ($cap !== 'archived_content') {
            return $caps;
        }

        // If the user does not have an agency context, they cannot view archived content.
        if (!Agency_Context::current_user_can_have_context()) {
            $caps[] = 'do_not_allow';
        }

        // Allowed agencies, use an array for future extensibility.
        $allowed_agencies = ['hq'];

        // If the user is not in an allowed agency, they cannot view archived content.
        if (!in_array(Agency_Context::get_agency_context(), $allowed_agencies)) {
            $caps[] = 'do_not_allow';
        }

        return $caps;
    }

    public function add_opt_in_out_quick_actions($actions, $post)
    {
        $is_opted_in = Agency_Editor::is_post_opted_in($post->ID);

        if (is_null($is_opted_in) || // User cannot opt-in to this post.
            $post->post_status !== 'publish' // The post is not published.
        ) {
            return $actions;
        }

        if ($is_opted_in) {
            $action = 'opt-out';
        } else {
            $action = 'opt-in';
        }

        $url = admin_url('post.php');
        $url = add_query_arg(array(
            'post' => $post->ID,
            'action' => $action,
        ), $url);
        $url = wp_nonce_url($url, 'opt_in_out-post_' . $post->ID);

        if ($is_opted_in) {
            $actions['opt_out'] = '<a href="' . $url . '" title="' . esc_attr__('Opt-out of this post') . '">' . _x('Opt-out', 'verb') . '</a>';
        } else {
            $actions['opt_in'] = '<a href="' . $url . '" title="' . esc_attr__('Opt-in to this post') . '">' . _x('Opt-in', 'verb') . '</a>';
        }

        return $actions;
    }

    /**
     * @return false|void
     */
    public function quick_action_opt_in_out()
    {
        if (!isset($_GET['action']) ||
            !in_array($_GET['action'], array('opt-in', 'opt-out')) ||
            !isset($_GET['post'])
        ) {
            return false;
        }

        $post_id = $_GET['post'];

        if (!isset($_GET['_wpnonce']) ||
            !wp_verify_nonce($_GET['_wpnonce'], 'opt_in_out-post_' . $post_id)
        ) {
            wp_die('Missing or invalid nonce.');
        }

        $post_type = get_post_type($post_id);
        if (in_array($post_type, $this->post_types, true)) {
            $action = $_GET['action'];
            $agency = Agency_Context::get_agency_context();
            $terms = [];
            $current_terms = wp_get_post_terms($post_id, 'agency');

            //wp_get_post_terms doesn't have the option to return just term slugs
            foreach ($current_terms as $term) {
                $terms[] = $term->slug;
            }

            if ($action == 'opt-in') {
                $terms[] = $agency;
            } else {
                if (($key = array_search($agency, $terms)) !== false) {
                    unset($terms[$key]);
                }
            }

            wp_set_object_terms($post_id, $terms, 'agency');

            wp_redirect(wp_get_referer());
            exit;
        }
    }
}
