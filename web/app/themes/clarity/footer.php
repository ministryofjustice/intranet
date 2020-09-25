<?php
/**
 * The template for displaying the footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */
?>

<footer class="c-footer">
  
  <div class="u-wrapper">

      <div class="l-primary">
        <?php
        if (! is_404()) :
            get_template_part('src/components/c-feedback-container/view');
        endif;

        get_template_part('src/components/c-footer-nav/view');

        ?>
      </div>

      <div class="l-secondary">
            <?php get_template_part('src/components/c-copyright-notice/view'); ?>
     </div>

  </div>

</footer>


<?php
  /**
   * wp_footer() required WP function do not remove. Used by plugins to hook into and for theme development.
   */
  wp_footer();
?>
</body>
</html>
