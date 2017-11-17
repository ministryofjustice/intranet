<?php 
/**
 *
 * Template Name: Blog Landing Page
 * 
 **/
get_header();
?>

<div class="l-main u-wrapper">
    <div class="l-full-page">
        <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    </div>
    <div class="l-secondary">
        
    </div>  
    <div class="l-primary">
        <div id="maincontent" class="u-wrapper l-main t-campaign">
            <?php get_component('c-blog-feed'); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>