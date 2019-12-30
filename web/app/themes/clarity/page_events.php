<?php
use MOJ\Intranet\Agency;
use MOJ\Intranet\EventsHelper;

/*
* Template Name: Events archive
*/
get_header();
?>
  <div id="maincontent" class="u-wrapper l-main t-article-list">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

    <div class="l-secondary" role="complementary">
        <?php get_template_part('src/components/c-content-filter/view', 'events'); ?>
    </div>

    <div class="l-primary" role="main">
        <?php
        $oAgency = new Agency();
        $activeAgency = $oAgency->getCurrentAgency();

        $agency_term_id = $activeAgency['wp_tag_id'];

        $EventsHelper  = new EventsHelper();
        $events = $EventsHelper->get_events($agency_term_id);

        if ($events) :
            echo '<h2 class="o-title o-title--section" id="title-section">Upcoming events</h2>';
            echo '<div id="content">';
            include locate_template('src/components/c-events-list/view.php');
            echo '</div>';
        else :
            echo 'No events are currently listed :(';
        endif;
        ?>
    </div>
  </div>

<?php
get_footer();
