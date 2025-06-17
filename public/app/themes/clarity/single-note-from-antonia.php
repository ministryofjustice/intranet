<?php

/*
* Single note-from-antonia post
*/

if (wp_redirect('/notes-from-antonia/#note-' . $post->ID)) {
    exit;
}

/**
 * The rest of this page is not presented. Instead, users are redirected to the main
 */

get_header();
?>

    <main role="main" id="maincontent" class="u-wrapper l-main t-news-article">
        <?php
        get_template_part('src/components/c-breadcrumbs/view', 'note-from-antonia');
        get_template_part('src/components/c-news-article/view-note');
        ?>
    </main>

<?php
get_footer();
