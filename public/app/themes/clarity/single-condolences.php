<?php
get_header();

?>
  <main role="main" id="maincontent" class="u-wrapper l-main t-blog-article">
    <?php
      get_template_part('src/components/c-breadcrumbs/view', 'condolence');
      get_template_part('src/components/c-condolence/view');
    ?>

    <section class="l-full-page">
      <?php
        get_template_part('src/components/c-comments/view', 'condolence');
        ?>
    </section>

  </main>

<?php
get_footer();
