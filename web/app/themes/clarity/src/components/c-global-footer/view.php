<?php
  $feedback_ack = '<div class="c-feedback__prompt" id="fbf_ack" tabindex="-1" autofocus>';
  $feedback_ack .= '<div class="feedback__prompt-success" style="input:focus" role="alert">';
  $feedback_ack .= 'Thank you for your feedback</div></div>';
  echo $feedback_ack;
?>
<footer class="c-footer">
  <div class="u-wrapper">
    <div class="l-primary">
      <?php get_template_part('src/components/c-feedback-container/view'); ?>
        <?php get_template_part('src/components/c-footer-nav/view'); ?>
    </div>
    <div class="l-secondary c-copyright">
        <?php get_template_part('src/components/c-copyright-notice/view'); ?>
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
