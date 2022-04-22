<?php

/*
* Single news post
*/

get_header();
?>

<main role="main" id="maincontent" class="u-wrapper l-main t-news-article" role="main">
    <?php
     get_template_part('src/components/c-breadcrumbs/view', 'news');
     get_template_part('src/components/c-news-article/view');
    ?>

  <section class="l-full-page">

    <?php
     get_template_part('src/components/c-last-updated/view');
     get_template_part('src/components/c-share-post/view');
     get_template_part('src/components/c-comments/view');
    ?>

  </section>

</main>

<?php
get_footer();
