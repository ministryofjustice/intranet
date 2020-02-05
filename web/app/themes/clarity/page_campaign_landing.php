<?php
use MOJ\Intranet\Agency;
use MOJ\Intranet\EventsHelper;

/*
* Template Name: Campaign hub template
*/

global $post;

$terms = get_the_terms($post->ID, 'campaign_category');
if ($terms) {
    foreach ($terms as $term) {
        $campaign_id = $term->term_id;
    }
}

get_header(); ?>
<div id="maincontent" class="u-wrapper l-main l-reverse-order t-hub">
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

  <div class="l-primary campaign" role="main">
        <?php
        get_template_part('src/components/c-campaign-hub-banner/view');
        get_template_part('src/components/c-article-excerpt/view');
        get_campaign_news_api($campaign_id);
        get_campaign_post_api($campaign_id);

        $oAgency = new Agency();
        $activeAgency = $oAgency->getCurrentAgency();

        $agency_term_id = $activeAgency['wp_tag_id'];
        $filter_options = ['campaign_filter' => $campaign_id];

        $EventsHelper  = new EventsHelper();
        $events = $EventsHelper->get_events($agency_term_id, $filter_options);

        if ($events) {
            echo '<h2 class="o-title o-title--section" id="title-section">Events</h2>';
            include locate_template('src/components/c-events-list/view.php');
        }
        ?>
  </div>
</div>

<?php
get_footer();
