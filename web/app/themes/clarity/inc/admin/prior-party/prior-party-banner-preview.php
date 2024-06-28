<?php

namespace MOJIntranet;

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
        $this->banner();
        $this->posts();
    }



    public function pageLoad(): void
    {
        $this->banners = get_field($this->repeater_name, 'option');
    }

    public function page(): void
    {
        echo "<h1>Prior Party Banner - Preview</h1>";

        echo "<h2>$this->banner_reference</h2>";
        //echo "<h2>Banners</h2><pre>" . print_r($this->banners, true) . "</pre>";


        if ($this->banner_reference) {
            // banner display
            echo '<div class="prior-party-banner">
                <div class="prior-party-banner__text">' . $this->banner["banner_content"] . '</div>
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
                    echo '<div class="ppb-posts__row">';
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

    private function displayBanners()
    {
        echo '<div class="ppb-banners">';
        echo '<div class="ppb-banners__row header">';
        echo '<div class="ppb-banner-col ppb-banners__title">Banner</div>';
        echo '<div class="ppb-banner-col ppb-banners__dates">Dates</div>';
        echo '<div class="ppb-banner-col ppb-banners__active">Active</div>';
        echo '</div>';

        foreach ($this->banners as $banner) {
            echo '<div class="ppb-banners__row" data-reference="' . $banner['reference'] . '">';
            echo '<div class="ppb-banner-col ppb-banners__title">' . $banner['banner_content'] . '</div>';
            echo '<div class="ppb-banner-col ppb-banners__dates">
                    <span class="ppb-banners__date_starts">' . $banner['start_date'] . '</span>
                    <span class="ppb-banners__date_stops">' . $banner['end_date'] . '</span>
                  </div>';
            echo '<div class="ppb-banner-col ppb-banners__active">' . $banner['banner_active'] . '</div>';
            echo '</div>';
        }

        echo '</div>';
    }

    private function banner(): void
    {
        foreach ($this->banners as $banner) {
            if ($this->banner_reference === $banner['reference']) {
                $this->banner = $banner;
            }
        }
    }

    private function posts(): void
    {
        if (($this->banner['start_date'] ?? false) && ($this->banner['end_date'] ?? false)) {
            $args = [
                'date_query' => [
                    [
                        'after' => $this->banner['start_date'],
                        'before' => $this->banner['end_date'],
                        'inclusive' => true
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
