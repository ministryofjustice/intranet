<?php
get_header();

?>
  <div id="maincontent" class="u-wrapper l-main t-blog-article" role="main">
    <?php
      get_template_part('src/components/c-breadcrumbs/view', 'condolence');
      get_template_part('src/components/c-condolence/view');
    ?>

    <section class="l-full-page">
    <?php
      get_template_part('src/components/c-comments/view', 'condolence');
    ?>
    </section>

  </div>

<?php
get_footer();
