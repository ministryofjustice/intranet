<?php

namespace MOJIntranet;

use Exception;
use MOJ\Intranet\Agency;
use WP_Error;
use WP_Query;
use WP_REST_Request;

class PriorPartyBannerPreview
{
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
     * @var string the reference of the selected banner
     */
    private mixed $banner_reference = null;

    /**
     * @var array all posts related to the date presented in the selected banner
     */
    private array $posts = [];

    /**
     * @var string the name of the page for previewing banner posts
     */
    private string $menu_slug = 'prior-party-banner-preview';

    /**
     * @var string normalised date format
     */
    private string $date_format = 'l jS \o\f F, Y';
    private string $date_format_short = 'jS F, Y';

    private array $post_type_labels = [];

    public function __construct()
    {
        global $wp_post_types;
        /**
         * Create options page for
         * - prior party settings
         * - prior party preview
         */
        add_action('init', [$this, 'priorPartyOptionPages']);
        add_action('admin_menu', [$this, 'menu']);

        add_action('rest_api_init', [$this, 'actionHandler']);

        /**
         * Don't load preview code until needed
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
        $this->post_type_labels = [
            'post' => get_post_type_object('post'),
            'news' => get_post_type_object('news'),
            'page' => get_post_type_object('page'),
            'note-from-antonia' => get_post_type_object('note-from-antonia')
        ];

        echo "<h1>Prior Party Banner - Preview</h1>";

        if ($this->banner_reference) {
            // drop return link
            echo '<a href="' . get_admin_url(
                    null,
                    'tools.php?page=prior-party-banner-preview'
                ) . '" class="banner-return-link">View all banners</a>';

            // get the banner
            $this->banner();

            // get the posts
            $this->posts();

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

            echo '<hr />';
            //echo '<pre>' . print_r($this->posts[0], true) . '</pre>';
            // list of posts falling within date range
            if (!empty($this->posts)) {
                echo '<div class="ppb-posts">';

                echo '<div class="ppb-posts__row header">';
                echo '<div class="ppb-post-col ppb-posts__title">Title</div>';
                echo '<div class="ppb-post-col ppb-posts__date">Date</div>';
                echo '<div class="ppb-post-col ppb-posts__type">Post type</div>';
                echo '<div class="ppb-post-col ppb-posts__agency">Agency</div>';
                echo '<div class="ppb-post-col ppb-posts__visibility">Visible</div>';
                echo '</div>';

                foreach ($this->posts as $post) {
                    $date = new \DateTime($post->post_date);
                    $agencies = $this->getPostAgencies($post->ID);
                    $status = get_field('prior_party_banner', $post->ID);
                    $link_admin = get_edit_post_link($post->ID);
                    $link_view = get_permalink($post->ID);
                    //echo '<pre>' . print_r($agencies, true) . '</pre>';

                    echo '<div class="ppb-posts__row" data-id="' . $post->ID . '">';
                    echo '<div class="ppb-post-col ppb-posts__title">' . $post->post_title . '<br>
                              <span class="nav-link"><a href="'.$link_view.'" target="_blank">View</a> | </span>
                              <span class="nav-link"><a href="'.$link_admin.'" target="_blank">Edit</a></span>
                          </div>';
                    echo '<div class="ppb-post-col ppb-posts__date">' . $date->format($this->date_format_short) . '</div>';
                    echo '<div class="ppb-post-col ppb-posts__type">' . $this->post_type_labels[$post->post_type]->labels->name . '</div>';
                    echo '<div class="ppb-post-col ppb-posts__agency">' . implode(' ', $agencies) . '</div>';
                    echo '<div class="ppb-post-col ppb-posts__status" data-status="' . ($status === false ? 'off' : 'on') . '"></div>';
                    echo '</div>';
                }
                echo '<div class="header-fixed"></div>';
                echo '</div>';
            }
        } else {
            $this->displayBanners();
        }
    }

    private function getPostAgencies($id)
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
        echo '<div class="ppb-banners">';
        echo '<div class="ppb-banners__row header">';
        echo '<div class="ppb-banner__col ppb-banners__title">Banner</div>';
        echo '<div class="ppb-banner__col ppb-banners__dates">Dates</div>';
        echo '</div>';

        foreach ($this->banners as $banner) {
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
     * @return void
     */
    private function posts(): void
    {
        if (($this->banner['start_date'] ?? false) && ($this->banner['end_date'] ?? false)) {
            $agency = new Agency();
            $active = $agency->getCurrentAgency();
            $args = [
                'post_type' => ['post', 'page', 'news', 'note-from-antonia'],
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

            $query = new WP_Query($args);

            if (!is_wp_error($query)) {
                $this->posts = $query->get_posts();
            }
        }
    }

    /**
     * Creates a menu link under the Tools section in the admin Dashboard
     *
     * @return void
     */
    public function menu(): void
    {
        $title = 'Prior Party Preview';
        $hook = add_submenu_page(
            'tools.php',
            $title,
            $title,
            'manage_options',
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
     * @return WP_Error|false|string
     */
    public function updateStatus(WP_REST_Request $request): WP_Error|false|string
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
}

new PriorPartyBannerPreview();
