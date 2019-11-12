<?php
use MOJ\Intranet\Agency;
use MOJ\Intranet\EventsHelper;

/**
 *
 * Template name: Region landing
 *
 */
$terms = get_the_terms(get_the_ID(), 'region');

if (is_array($terms)) :
    foreach ($terms as $term) :
        $region_id = $term->term_id;
    endforeach;
endif;

get_header();
?>

  <div id="maincontent" class="u-wrapper l-main l-reverse-order t-default">

    <?php get_template_part('src/components/c-breadcrumbs/view', 'region'); ?>

    <div class="l-secondary">
        <?php get_template_part('src/components/c-left-hand-menu/view'); ?>
    </div>
    <div class="l-primary" role="main">

      <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

        <?php
        echo '<div id="content">';
        get_news_api('regional_news');
        echo '</div>';

        echo '<br><div id="content">';

        $oAgency = new Agency();
        $activeAgency = $oAgency->getCurrentAgency();

        $agency_term_id = $activeAgency['wp_tag_id'];
        $filter_options = ['region_filter' => $region_id];

        $EventsHelper  = new EventsHelper();
        $events = $EventsHelper->get_events($agency_term_id, $filter_options);
        if ($events) {
            echo '<h2 class="o-title o-title--section" id="title-section">Events</h2>';
            include locate_template('src/components/c-events-list/view.php');
        }
        echo '</div>';
        ?>
    </div>
  </div>

<?php
get_footer();
