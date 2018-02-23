<?php
use MOJ\Intranet\Agency;
/*
* Template Name: Campaign Hub Clarity page
*/
if (!defined('ABSPATH')) {
    die();
}


get_header();?>
<div id="maincontent" class="u-wrapper l-main l-reverse-order t-hub" data-id="<?php echo $post->ID; ?>">
    <h1 class="o-title o-title--page">Hub Page</h1>
    <?php 
        global $post;
        $terms = get_the_terms($post->ID, 'campaign_category');
        foreach ($terms as $term) {
            $campaign_id = $term->term_id;
        } 
        $terms = get_the_terms($post->ID, 'region');
        foreach ($terms as $term) {
            $region_id = $term->term_id;
        }    
        
    ?>
    <div class="l-secondary">
        <?php //get_template_part('src/components/c-left-hand-menu/view'); ?>
        <?php //get_component('c-content-filter'); ?>
    </div>
    <div class="l-primary" role="main">
        <h2 class="o-title o-title--section" id="title-section">Latest Region News</h2>
        <div id="content">
            <?php get_region_news_api($region_id); ?>
        </div>
        
        <h2 class="o-title o-title--section">Posts</h2>
        <?php get_campaign_post_api($campaign_id); ?>
    </div>
</div>
<?php get_footer(); ?>