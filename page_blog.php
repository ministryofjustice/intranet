<?php 
/**
 *
 * Template Name: Blog Landing Page
 * 
 **/
use MOJ\Intranet\Agency;
$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

get_header();

$archives_args = array( 
    'type' => 'monthly', 
    'format' => 'custom',
    'show_post_count' => false 
);

$year = '';
$month = '';
$keyword = sanitize_text_field( $_POST[ 'ff_keywords_filter' ] );;
$agency_name = $activeAgency['shortcode'];

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$args = array(
    'paged' => $paged,
    'posts_per_page' => 5,
    'post_type' => 'post',
    'post_status' => 'publish',
    'tax_query' => array(
		array(
			'taxonomy' => 'agency',
			'field'    => 'slug',
			'terms'    => $agency_name,
		),
	),
    's' => $keyword,
    'year'  => $year,
    'monthnum' => $month,
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
        
        <?php $prefix = 'ff'; ?>
        <section class="c-content-filter">
            <p>The results will update automatically based on your selections.</p>
            <form action="" id="<?php echo $prefix; ?>" action="post">
                <div class="c-input-container c-input-container--select">
                <label for="ff_date_filter">Date<span class="c-input-container--required">*</span>
                :</label>
                    <select name="ff_date_filter" id="ff_date_filter" required="required">
                        <?php
                            wp_get_archives( $archives_args );
                        ?>
                    </select>
                </div>
                <?php
                form_builder('text', $prefix, 'Keywords', 'keywords_filter', null, null, 'Keywords', null, true, null, null);
                ?>
            </form>
        </section>
    </div>  
    <div class="l-primary">
        <div id="maincontent" class="u-wrapper l-main t-campaign">
            <section class="c-blog-feed">
                <h1 class="o-title o-title--section">Latest</h1>
                <div id="content">
                <?php
                    if ( $query->have_posts() ) {
                        while ( $query->have_posts() ) {    
                            $query->the_post();
                            get_component('c-article-item', '', 'show_excerpt');
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