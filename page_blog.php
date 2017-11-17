<?php 
/**
 *
 * Template Name: Blog Landing Page
 * 
 **/
get_header();

$archives_args = array( 
    'type' => 'monthly', 
    'format' => 'option', 
    'show_post_count' => false 
);

$year = 2017;
$month = 04;

$args = array(
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
        <select name="archive-dropdown">
            <option value=""><?php echo esc_attr( __( 'Select Month' ) ); ?></option> 
            <?php wp_get_archives( $archives_args ); ?>
        </select>
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