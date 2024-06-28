<?php

namespace MOJIntranet;

use WP_Query;

class PriorPartyBannerPreview
{
    private array $banner = [];
    private string $repeater_name = 'prior_political_party_banners';
    private mixed $banner_reference = null;
    private array $posts = [];

    private string $menu_slug = 'prior-party-banner-preview';

    public function __construct()
    {
        $current_page = sanitize_text_field($_GET['page'] ?? '');
        if ($current_page !== $this->menu_slug) {
            return;
        }

        $banner_reference = sanitize_text_field($_GET['ref'] ?? '');
        if ($banner_reference === '') {
            $banner_reference = 'not-found';
        }

        $this->banner_reference = $banner_reference;
        $this->banner();
        $this->posts();

        add_action('admin_menu', [$this, 'menu']);
    }


    public function menu(): void
    {
        $hook = add_submenu_page(
            '',
            'Prior Party Banner - Preview',
            'Prior Party Banner - Preview',
            'manage_options',
            $this->menu_slug,
            [$this, 'page']
        );

        add_action("load-$hook", [$this, 'pageLoad']);
    }


    public function pageLoad(): void
    {
        //
    }

    public function page(): void
    {
        echo "<h1>Prior Party Banner - Preview</h1>";
        echo "<h2>$this->banner_reference</h2>";

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
    }

    private function banner(): void
    {
        foreach (get_field($this->repeater_name, 'option') as $banner) {
            if ($this->banner_reference === $banner['reference']) {
                $this->banner = $banner;
            }
        }
    }

    private function posts(): void
    {
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

new PriorPartyBannerPreview();
