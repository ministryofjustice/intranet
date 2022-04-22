<?php

/*
* Single event page
*/

get_header();
?>
  <main role="main" id="maincontent" class="u-wrapper l-main t-events">
    <?php
    get_template_part('src/components/c-breadcrumbs/view', 'team');
    get_template_part('src/components/c-event-article/view', 'team');
    ?>

    <section class="l-full-page">
    <?php get_template_part('src/components/c-share-post/view'); ?>
    </section>

</main>
<?php
get_footer();
