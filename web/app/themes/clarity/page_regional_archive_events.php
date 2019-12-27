<?php
use MOJ\Intranet\Agency;
use MOJ\Intranet\EventsHelper;

/**
 *
 * Template name: Region archive events
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
  <div id="maincontent" class="u-wrapper l-main t-regional-archive">
    <?php get_template_part('src/components/c-breadcrumbs/view', 'region-single'); ?>

    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

    <div class="l-secondary" role="complementary" data-termid="<?php echo $region_id; ?>">
        <?php get_template_part('src/components/c-content-filter/view', 'region-events'); ?>
    </div>

    <div class="l-primary" role="main">

        <?php
            $oAgency = new Agency();
            $activeAgency = $oAgency->getCurrentAgency();

            $agency_term_id = $activeAgency['wp_tag_id'];
            $filter_options = ['region_filter' => $region_id];

            $EventsHelper  = new EventsHelper();
            $events = $EventsHelper->get_events($agency_term_id, $filter_options);
        if ($events) {
            echo '<h2 class="o-title o-title--section" id="title-section">Events</h2>';
            echo '<div id="content">';
            include locate_template('src/components/c-events-list/view.php');
            echo '</div>';
        } else {
            echo 'No events are currently listed :(';
        }

        ?>

    </div>
  </div>

<?php
get_footer();
