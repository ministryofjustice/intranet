<!-- c-share-post starts here -->
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<section class="c-share-post">
  <ul class="c-share-post__link">

    <li><?php echo do_shortcode('[likebutton]'); ?><li>

      <!--AF: I feel like this should be a form rather than a mailto link -->
      <span class="u-icon u-icon--redo2"></span>
      <a href="mailto:?subject=<?php echo get_the_title(); ?>&body=A colleague thought you would be intrested in this intranet article <?php echo get_permalink(); ?>">Share this post by email</a>
    </li>
  </ul>
</section>
<?php endwhile; else : ?>
	<p><?php esc_html_e('Sorry, nothing was found.'); ?></p>
<?php endif; ?>
<!-- c-share-post ends here -->
