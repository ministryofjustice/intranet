<?php
/**
 * Template Name: Search
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package WordPress
 * @subpackage Clarity
 * @since 1.0
 * @version 1.0
 */
wp_enqueue_script('googleOptimizeAB', 'https://www.googleoptimize.com/optimize.js?id=OPT-5MGTCQZ');
do_action( 'qm/start', 'get_header' );
get_header();
do_action( 'qm/stop', 'get_header' );
?>

    <main role="main" id="maincontent" class="u-wrapper l-main t-search-results">

        <h1 class="o-title o-title--page">Search</h1>
        <div class="l-secondary" role="complementary">
            <?php 
            do_action( 'qm/start', 'filter' );
            get_template_part('src/components/c-search-results-filter/view');
            do_action( 'qm/stop', 'filter' );
             ?>
        </div>

        <div class="l-primary">
            <div id="content">

                <?php if (have_posts()) : ?>
                    <h1 class="o-title o-title--byline"><?php printf(__('Search Results for: %s', 'clarity'), '&nbsp;<span>' . get_search_query() . '</span>'); ?></h1>
                    <?php echo $wp_query->found_posts . ' results found'; ?>
                <?php else : ?>
                    <h1 class="o-title o-title--byline"><?php _e('Nothing found', 'clarity'); ?></h1>
                <?php endif; ?>

                <?php
                if (have_posts()) :
                    do_action( 'qm/start', 'while_have_posts' );
                    while (have_posts()) :
                        the_post();
                        get_template_part('src/components/c-search-results/view');
                    endwhile;
                    do_action( 'qm/stop', 'while_have_posts' );

                    do_action( 'qm/start', 'pagination' );
                    get_template_part('src/components/c-pagination/view');
                    do_action( 'qm/stop', 'pagination' );
                else :
                    ?>
                    <p><?php _e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'clarity'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php
get_footer();
