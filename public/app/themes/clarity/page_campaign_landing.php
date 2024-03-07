<?php

use MOJ\Intranet\Agency;
use MOJ\Intranet\EventsHelper;

/*
* Template Name: Campaign hub template
*/

global $post;

$campaign_id = null;
$terms = get_the_terms($post->ID, 'campaign_category');
if ($terms) {
    foreach ($terms as $term) {
        $campaign_id = $term->term_id;
    }
}

get_header(); ?>
    <main role="main" id="maincontent" class="u-wrapper l-main l-reverse-order t-hub">
        <?php get_template_part('src/components/c-breadcrumbs/view'); ?>

        <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

        <?php
        $excerpt = get_field('dw_excerpt');
        if (strlen($excerpt) > 0) {
            ?>
            <section class="c-article-excerpt">
                <p><?php echo $excerpt; ?></p>
            </section>
            <?php
        }
        ?>

        <div class="l-secondary">
            <?php
            get_template_part('src/components/c-left-hand-menu/view');
            ?>
        </div>

        <div class="campaign">
            <?php
            get_template_part('src/components/c-campaign-hub-banner/view');
            get_template_part('src/components/c-article-excerpt/view');

            // check for populated campaign ID
            if ($campaign_id !== null) {
                get_campaign_news_api($campaign_id);
                get_campaign_post_api($campaign_id);

                $oAgency = new Agency();
                $activeAgency = $oAgency->getCurrentAgency();

                $eventHelper = new EventsHelper();
                $events = $eventHelper->get_events($activeAgency['wp_tag_id'], ['campaign_filter' => $campaign_id]);

                if ($events) {
                    echo '<h2 class="o-title o-title--section" id="title-section">Events</h2>';
                    include locate_template('src/components/c-events-list/view.php');
                }
            } else {
                echo '<h2 class="o-title o-title--section" id="title-section">Almost ready</h2>';
                echo '<p>Content will appear here once a campaign category has been allocated.</p>';

                if (current_user_can('edit_posts')) {
                    echo '<strong>Editors:</strong> <a href="' . get_edit_post_link() . '">select a campaign category</a>.';
                }
            }
            ?>
        </div>
    </main>

<?php
get_footer();
