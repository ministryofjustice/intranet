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

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$args = array(
    'paged' => $paged,
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

$prev_page_number = $paged-1;
$next_page_number = $paged+1;

$total_page_number = $query->max_num_pages;

?>

<div class="l-main u-wrapper">
    <div class="l-full-page">
        <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    </div>
    <div class="l-secondary">
        <select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;">
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
            <nav class="c-pagination" role="navigation" aria-label="Pagination Navigation">
                <?php 
                    echo previous_posts_link( '<span class="c-pagination__main">Previous page</span><span class="c-pagination__count">'.$prev_page_number.' of '.$total_page_number.'</span>' );
                    echo next_posts_link( '<span class="c-pagination__main">Next page</span><span class="c-pagination__count">'.$next_page_number.' of '.$total_page_number.'</span>', $total_page_number );          
                ?>
            </nav> 
        </div>
    </div>
</div>

<?php get_footer(); ?>