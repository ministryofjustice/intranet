<?php
/*
* Template Name: Book of Condolences
*/
get_header();

?>
    <div id="maincontent" class="u-wrapper l-main t-article-list">
        <?php get_template_part('src/components/c-condolence-header/view'); ?>
        <div class="l-secondary">
            <?php get_template_part('src/components/c-content-filter/view', 'condolences'); ?>
        </div>
        <div class="l-primary" role="main">
            <div id="content">
                <?php get_template_part('src/components/c-condolences-list/view'); ?>
            </div>
        </div>
    </div>

<?php
get_footer();
