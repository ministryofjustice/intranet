<?php
/*
* Removed Condolence template until ready to add back in
*/
get_header();

?>
    <main role="main" id="maincontent" class="u-wrapper l-main t-article-list">
        <?php get_template_part('src/components/c-condolence-header/view'); ?>
            <div class="l-secondary">
                <?php get_template_part('src/components/c-content-filter/view', 'condolences'); ?>
            </div>
            <div class="l-primary">
                <div id="content">
                    <?php get_template_part('src/components/c-condolences-list/view'); ?>
                </div>
            </div>
    </main>

<?php
get_footer();
