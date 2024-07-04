<?php

namespace MOJIntranet;

use Exception;
use MOJ\Intranet\Agency;
use WP_Error;
use WP_Query;
use WP_REST_Request;

require_once 'prior-party-banner-events.php';

class PriorPartyBannerAdmin
{

    use PriorPartyBannerTrackEvents;

    /**
     * @var array contains all available banners
     */
    private array $banners = [];

    /**
     * @var array contains the selected banner
     */
    private array $banner = [];

    /**
     * @var string defines tha name of the ACF repeater field
     */
    private string $repeater_name = 'prior_political_party_banners';

    /**
     * @var string defines the name of the ACF field on the posts
     */
    private string $post_field_name = 'prior_party_banner';

    /**
     * @var string the reference of the selected banner
     */
    private mixed $banner_reference = null;

    /**
     * @var array all posts related to the date presented in the selected banner
     */
    private array $posts = [];

    /**
     * @var string the name of the page for viewing banner posts
     */
    private string $menu_slug = 'prior-party-banners';

    /**
     * @var bool should we review tracked events on this page?
     */
    private bool $review_tracked_events = false;

    /**
     * @var int|null review events from this time
     */
    private int | null $review_tracked_events_from = null;

    /**
     * @var int|null review events up to this time
     */
    private int | null $review_tracked_events_to = null;

    /**
     * @var string normalised date format
     */
    private string $date_format = 'l jS \o\f F, Y';
    private string $date_format_short = 'jS F, Y';
    private string $date_format_time = 'jS F, Y - g:i a';

    public function __construct()
    {
        global $wp_post_types;
        /**
         * Create options page for
         * - prior party settings
         * - prior party view
         */
        add_action('init', [$this, 'priorPartyOptionPages']);
        add_action('admin_menu', [$this, 'editorToolsMenu']);
        add_action('admin_menu', [$this, 'menu']);

        add_action('rest_api_init', [$this, 'actionHandler']);

        add_filter('acf/update_value/name=' . $this->post_field_name, [$this, 'trackBannerUpdates'], 10, 4);

        /**
         * Don't load view code until needed
         */
        $current_page = sanitize_text_field($_GET['page'] ?? '');
        if ($current_page !== $this->menu_slug) {
            return;
        }

        $banner_reference = sanitize_text_field($_GET['ref'] ?? '');
        if ($banner_reference === '') {
            $banner_reference = null;
        }

        $this->banner_reference = $banner_reference;

        /**
         * Parse the query string for review time-frame
         */
        $tracked_events = $_GET['review_tracked_events'] ?? false;
        if ($tracked_events === 'true') {
            $this->review_tracked_events = true;
            $this->review_tracked_events_from = (int) $_GET['events_from'] ?: null;
            $this->review_tracked_events_to =  (int) $_GET['events_to'] ?: null;
        }
    }

    /**
     * Loaded via a hook
     *
     * @return void
     */
    public function pageLoad(): void
    {
        $this->banners = get_field($this->repeater_name, 'option');
    }

    /**
     * Build the page
     *
     * @throws Exception
     */
    public function page(): void
    {
        // housekeeping
        $post_type_labels = [];
        $post_type_labels = [
            'post' => get_post_type_object('post'),
            'news' => get_post_type_object('news'),
            'page' => get_post_type_object('page'),
            'note-from-antonia' => get_post_type_object('note-from-antonia')
        ];

        echo "<h1>Prior Party Banners</h1>";

        if ($this->banner_reference) {
            // drop return link
            echo '<a href="' . get_admin_url(
                null,
                'admin.php?page=' . $this->menu_slug
            ) . '" class="ppb-cta-link">View all banners</a>';

            // get and cache the banner
            $this->banner();

            $posts_in = null;

            $events = [];
            if ($this->review_tracked_events) {
                $events = $this->getTrackEvents(null, $this->review_tracked_events_from, $this->review_tracked_events_to);
                $posts_in = array_keys($events);
            }

            // get and cache the posts
            $this->posts($posts_in);

            // normalise the dates
            $start = new \DateTime($this->banner["start_date"]);
            $stop = new \DateTime($this->banner["end_date"]);


            // display the banner
            echo '<div class="prior-party-banner">
                    <div class="prior-party-banner__text">' . $this->banner["banner_content"] . '</div>
                  </div>
                  <div class="prior-party-banner__dates">
                    <div class="banner__date start">Active: <span>' . $start->format($this->date_format) . '</span></div>
                    <div class="banner__date end">Ended: <span>' . $stop->format($this->date_format) . '</span></div>
                  </div>';

            echo '<div class="info-description">';
            echo '<h2>Affected Content</h2>';
            echo '<p><strong>Total items: </strong><span id="total-count">' . count($this->posts) . '</span></p>';
            echo '<p>A list of content that will present a banner is displayed below.<br>The list is interactive.
                     For example, you can click an item to remove the<br>banner or, filter results using the input, just
                     type a word.</p>';

            echo '<h3>Filter</h3>';
            echo '<p>Type a word in the input field to filter content rows.</p>';
            echo '<input id="search-input" class="filter-rows-input" placeholder="Filter words">
                  <a href="#" id="clear-filter" class="ppb-cta-link disabled">Clear</a>';
            echo '</div><br />';

            //echo '<pre>' . print_r($this->posts[0], true) . '</pre>';

            // list of posts falling within date range
            if (!empty($this->posts)) {
                echo '<div id="ppb-posts">';

                echo '<div class="ppb-posts__row header">';
                echo '<div class="ppb-post-col ppb-posts__title">Title</div>';
                echo '<div class="ppb-post-col ppb-posts__date">Date</div>';
                echo '<div class="ppb-post-col ppb-posts__type">Type</div>';
                echo '<div class="ppb-post-col ppb-posts__agency">Agency</div>';
                // Add an extra header column if we're reviewing tracked changes.
                if ($this->review_tracked_events) {
                    echo '<div class="ppb-post-col ppb-posts__review">Review</div>';
                }
                echo '<div class="ppb-post-col ppb-posts__visibility">Visible</div>';
                echo '</div>';

                foreach ($this->posts as $post) {
                    $date = new \DateTime($post->post_date);
                    $agencies = $this->getPostAgencies($post->ID);
                    $status = get_field('prior_party_banner', $post->ID);

                    // links
                    $link_admin = get_edit_post_link($post->ID);
                    $link_view = get_permalink($post->ID) . '?time_context=' . $stop->format('U');

                    // latest event
                    $event_data = $this->getTrackedDisplayString($post->ID);
                    // Transform the events' assoc. array into a readable format.
                    $readable_events = array_map([$this, 'eventToReadableFormat'], $events[$post->ID] ?: []);
                    //echo '<pre>' . print_r($agencies, true) . '</pre>';

                    echo '<div class="ppb-posts__row" data-id="' . $post->ID . '">';
                    echo '<div class="ppb-post-col ppb-posts__title">' . $post->post_title . '<br>
                              <span class="nav-link"><a href="' . $link_view . '" target="_blank">View</a> | </span>
                              <span class="nav-link"><a href="' . $link_admin . '" target="_blank">Edit</a></span>';

                    if (isset($event_data['date'])) {
                        echo '<span class="event-data tool-tip" title-new="' .$event_data['date'] . '">' . $event_data['text'] . '</span>';
                    }

                    echo '</div>';
                    echo '<div class="ppb-post-col ppb-posts__date">' . $date->format($this->date_format_short) . '</div>';
                    echo '<div class="ppb-post-col ppb-posts__type">' . $post_type_labels[$post->post_type]->labels->name . '</div>';
                    echo '<div class="ppb-post-col ppb-posts__agency">' . implode(' ', $agencies) . '</div>';
                    // Add an extra body column if we're reviewing tracked changes.
                    if ($this->review_tracked_events) {
                        echo '<div class="ppb-post-col ppb-posts__review">' . implode('<br/><br/>', $readable_events) . '</div>';
                    }
                    echo '<div class="ppb-post-col ppb-posts__status" data-status="' . ($status === false ? 'off' : 'on') . '"></div>';
                    echo '</div>';
                }
                echo '<div id="header-fixed"></div>';
                echo '</div>';
                echo '<div id="back-to-top" title="Back to top"></div>';
            }
        } else {
            $this->displayBanners();
        }
    }

    private function getPostAgencies($id): array
    {
        $agencies = get_the_terms($id, 'agency');
        $result = [];
        foreach ($agencies as $agency) {
            $result[] = '<span class="agency-name">' . $agency->name . '</span>';
        }

        return $result;
    }

    /**
     * First load of the page, let's display available banners
     *
     * @return void
     * @throws Exception
     */
    private function displayBanners(): void
    {
        echo '<p>Please select a banner from the list.<br>You can view all content items that will have the
                 banner displayed.</p>';
        echo '<div class="ppb-banners">';
        echo '<div class="ppb-banners__row header">';
        echo '<div class="ppb-banner__col ppb-banners__title">Banner</div>';
        echo '<div class="ppb-banner__col ppb-banners__dates">Dates</div>';
        echo '</div>';

        foreach ($this->banners as $banner) {
            if ($banner['banner_active'] === false) {
                continue;
            }

            // readable dates
            $start_date = new \DateTime($banner['start_date']);
            $end_date = new \DateTime($banner['end_date']);

            echo '<div class="ppb-banners__row" data-reference="' . $banner['reference'] . '">';
            echo '<div class="ppb-banner__col ppb-banners__title">
                    <div class="prior-party-banner">
                        <div class="prior-party-banner__text">' . $banner['banner_content'] . '</div>
                    </div>
                  </div>';

            echo '<div class="ppb-banner__col ppb-banners__dates">
                    <span class="ppb-banners__date_starts"><span>Active:</span> ' . $start_date->format($this->date_format) . '</span>
                    <span class="ppb-banners__date_stops"><span>Ended:</span> ' . $end_date->format($this->date_format) . '</span>
                  </div>';
            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * Selects a banner by reference
     *
     * @return void
     */
    private function banner(): void
    {
        //echo '<pre>' . print_r($this->banner, true) . '</pre>';

        foreach ($this->banners as $banner) {
            if ($this->banner_reference === $banner['reference']) {
                $this->banner = $banner;
            }
        }
    }

    /**
     * Search the DB for posts that match the Agency and date range provided
     *
     * @param null|array $post_ids an optional array to filter the posts
     *
     * @return void
     */
    private function posts(null | array $posts_in): void
    {
        if (($this->banner['start_date'] ?? false) && ($this->banner['end_date'] ?? false)) {
            $agency = new Agency();
            $active = $agency->getCurrentAgency();
            $args = [
                'post_type' => ['post', 'page', 'news', 'note-from-antonia'],
                'post_status' => ['publish', 'pending'],
                'date_query' => [
                    [
                        'after' => $this->banner['start_date'],
                        'before' => $this->banner['end_date'],
                        'inclusive' => true
                    ]
                ],
                'tax_query' => [
                    [
                        'taxonomy' => 'agency',
                        'field' => 'term_id',
                        'terms' => $active['wp_tag_id']
                    ]
                ],
                'posts_per_page' => -1
            ];

            if ($posts_in) {
                $args['post__in'] = $posts_in;
            }

            $query = new WP_Query($args);

            if (!is_wp_error($query)) {
                $pp_banner = new PriorPartyBanner();
                $pp_banner->init();

                $this->posts = array_filter(
                    $query->get_posts(),
                    fn ($post) => $pp_banner->isValidLocation($post->ID)
                );
            }
        }
    }

    /**
     * Create a top level menu for editors.
     *
     * @return void
     */

     public function editorToolsMenu(): void
     {
         add_menu_page(
             'Editor Tools',
             'Editor Tools',
             'edit_posts',
             'editor-tools',
             [$this, 'editorToolsPage'],
             'dashicons-admin-tools',
             60
         );
     }

     public function editorToolsPage()
     {
        echo '<h1>Editor Tools</h1>';
     }

    /**
     * Creates a menu link under the Tools section in the admin Dashboard
     *
     * @return void
     */
    public function menu(): void
    {
        $title = 'Prior Party Banners';
        $hook = add_submenu_page(
            'editor-tools',
            $title,
            $title,
            'edit_posts',
            $this->menu_slug,
            [$this, 'page'],
            8
        );

        add_action("load-$hook", [$this, 'pageLoad']);
    }

    /**
     * Create the ACF settings page menu item, located under Tools
     *
     * @return void
     */
    public function priorPartyOptionPages(): void
    {
        $title = 'Prior Party Settings';

        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(
                array(
                    'page_title' => $title,
                    'capability' => 'manage_options',
                    'icon_url' => 'dashicons-calendar',
                    'menu_title' => $title,
                    'menu_slug' => 'prior-party-settings',
                    'parent_slug' => 'tools.php',
                    'position' => 7
                )
            );
        }
    }

    public function actionHandler(): void
    {
        register_rest_route(
            "prior-party/v2",
            "/update",
            [
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => function (\WP_REST_Request $request) {
                    return is_user_logged_in();
                },
                'callback' => [$this, 'updateStatus']
            ]
        );
    }

    /**
     * @param WP_REST_Request $request
     *
     * @return false|string
     */
    public function updateStatus(WP_REST_Request $request): false|string
    {
        $id = $request->get_param('id');
        $status = $request->get_param('status');

        // perform the update
        $result = update_field('prior_party_banner', ($status === 'off'), $id);

        $state = [
            'old' => 'tick',
            'new' => 'cross'
        ];

        // let's switch if needed...
        if ($status === 'off') {
            $state = [
                'old' => 'cross',
                'new' => 'tick'
            ];
        }

        return json_encode(["message" => (is_wp_error($result) ? $result : $state)]);
    }

    /**
     * Track updates to the prior_party_banner field.
     *
     * This function is triggered when the prior_party_banner field is updated.
     * Includes via: $this->updateStatus and via the ACF UI on the edit screen.
     *
     * @see https://www.advancedcustomfields.com/resources/acf-update_value/
     *
     * @param bool $value The field value.
     * @param int $post_id The post ID where the value is saved.
     *
     * @return bool The field value - unchanged.
     */

    public function trackBannerUpdates(bool $value, int $post_id): bool
    {
        $this->createTrackEvent($value, $post_id);

        return $value;
    }

    private function getTrackedDisplayString($post_id): array
    {
        $latest = $this->getLatestEvent($post_id);

        $event_data = [];
        if ($latest['tracked']) {
            // redact name if current user is not administrator
            $name = (current_user_can('manage_options') ? $latest['name'] : 'A user');

            // create the display string
            $event_data = [
                'date' => $latest['date'] . ', ' . $latest['time'],
                'text' => $name . ' from ' . $latest['agency'] . ' ' . $latest['action'] . ' the banner'
            ];
        }

        return $event_data;
    }
}

new PriorPartyBannerAdmin();
