<?php

namespace MOJIntranet;

use WP_Query;

class PriorPartyBannerPreview
{
    private array $banner = [];
    private string $repeater_name = 'prior_party_banners';
    private mixed $banner_id = null;
    private array $posts = [];

    public function __construct()
    {
        $this->banner_id = $_GET['banner_id'];
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
            'prior-party-banner-preview',
            [$this, 'page']
        );

        add_action("load-$hook", [$this, 'pageLoad']);
    }


    public function pageLoad(): void
    {

    }

    public function page()
    {
        echo "<h1>Prior Party Banner - Preview</h1>";
        echo "<h2>$this->banner_id</h2>";

        // banner display
        echo '<div class="prior-party-banner">
                <div class="prior-party-banner__text">' . $this->banner["text"] . '</div>
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
        if (have_rows($this->repeater_name)) :
            while (have_rows($this->repeater_name)) :
                the_row();
                $tab_count = 0;
                try {
                    $tab_count = count(get_field($this->repeater_name));
                } catch (\Exception) {
                }

                if ($tab_count) :
                    if (get_field($this->repeater_name)) :
                        while (the_repeater_field('banners')) :
                            $this->banner['text'] = get_sub_field('text');
                            $this->banner['start'] = get_sub_field('start_date');
                            $this->banner['end'] = get_sub_field('end_date');
                        endwhile;
                    endif;
                endif; // if tab_count is set
            endwhile;
        endif;

        $this->banner['text'] = 'This was published under the 2015 to 2024 Conservative government';
        $this->banner['start'] = '2015-05-05';
        $this->banner['end'] = '2024-07-05';
    }

    private function posts(): void
    {
        $args = [
            'date_query' => [
                [
                    'after' => $this->banner['start'],
                    'before' => $this->banner['end'],
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
