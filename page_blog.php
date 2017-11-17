<?php 
/**
 *
 * Template Name: Blog Landing Page
 * 
 **/
get_header();

$year = 2017;
$month = 04;

$args = array(
    'max_num_pages' => 5,
    'posts_per_page' => 5,
    'post_type' => 'post',
    'date_query' => array(
        array(
            'year'  => $year,
            'month' => $month,
        ),
    ),
);
$query = new WP_Query( $args );
?>

<div class="l-main u-wrapper">
    <div class="l-full-page">
        <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    </div>
    <div class="l-secondary">
        
    </div>  
    <div class="l-primary">
        <div id="maincontent" class="u-wrapper l-main t-campaign">
            <section class="c-blog-feed">
                <h1 class="o-title o-title--section">Latest</h1>
                <div>
                <?php
                    
                    if ( $query->have_posts() ) {
                        while ( $query->have_posts() ) {
                    
                            $query->the_post();
                            
                            get_component('c-article-item');
                        }
                    }
                wp_reset_postdata();
                ?>
                </div>
            </section>
        </div>
    </div>
</div>

<?php get_footer(); ?>