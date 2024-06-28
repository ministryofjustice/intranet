<?php

namespace MOJIntranet;

use Exception;
use MOJ\Intranet\Agency;
use WP_Query;

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

    public function __construct()
    {
        /**
         * Create options page for
         * - prior party settings
         * - prior party preview
         */
        add_action('init', [$this, 'priorPartyOptionPages']);
        add_action('admin_menu', [$this, 'menu']);

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

            // list of posts falling within date range
            if (!empty($this->posts)) {
                echo '<div class="ppb-posts">';

                echo '<div class="ppb-posts__row header">';
                echo '<div class="ppb-post-col ppb-posts__title">Title</div>';
                echo '<div class="ppb-post-col ppb-posts__date">Date</div>';
                echo '<div class="ppb-post-col ppb-posts__type">Post type</div>';
                echo '<div class="ppb-post-col ppb-posts__agency">Agency</div>';
                echo '</div>';

                foreach ($this->posts as $post) {
                    echo '<div class="ppb-posts__row" data-id="'. $post->ID . '">';
                    echo '<div class="ppb-post-col ppb-posts__title">' . $post->post_title . '</div>';
                    echo '<div class="ppb-post-col ppb-posts__date">' . $post->post_date . '</div>';
                    echo '<div class="ppb-post-col ppb-posts__type">' . $post->post_type . '</div>';
                    echo '<div class="ppb-post-col ppb-posts__agency"></div>';
                    echo '</div>';
                }
                echo '</div>';
            }
        } else {
            $this->displayBanners();
        }
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
                    <span class="ppb-banners__date_starts"><span>Active:</span> ' . $start_date->format(
                $this->date_format
            ) . '</span>
                    <span class="ppb-banners__date_stops"><span>Ended:</span> ' . $end_date->format(
                $this->date_format
            ) . '</span>
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
}

new PriorPartyBannerPreview();
