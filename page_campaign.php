<?php
/**
 * Template name: Campaign content
 */
?>
<?php
    //LEGACY
    $lhs_menu_on = get_field('dw_lhs_menu_on') == true;
?>

<?php get_header(); ?>
<?php get_component('c-dynamic-style'); ?>
<div class="l-main u-wrapper">
<?php //LEGACY ?>
    <?php if($lhs_menu_on == true): ?>
        <div class="l-secondary">
            <?php get_component('c-left-hand-menu'); ?>
        </div>
    <?php endif ?>     
    <div class="<?php
            echo ($lhs_menu_on == true ? 'l-primary' : 'l-full-page')
        ?>">
        <?php //LEGACY END ?>
        <div id="maincontent" class="u-wrapper l-main t-campaign">
            <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
            <div class="l-full-page" role="main">
                <?php get_component('c-full-width-banner'); ?>
                <?php
                    while ( have_posts() ) : the_post();
                        get_template_part( 'src/components/c-rich-text-block/view' );
                    endwhile; // End of the loop.
                ?>
            </div>
        </div>
    </div>

</div>
<?php get_component('c-global-footer'); ?>
