<?php
if (have_posts()) :
    while (have_posts()) :
        the_post();
        ?>

<!-- c-rich-text-block starts here -->
<section class="c-rich-text-block">
    <?php echo apply_filters( 'the_content', get_sub_field(the_content())); ?>
    
</section>
<!-- c-rich-text-block ends here -->

    <?php endwhile;
else : ?>
<p><?php esc_html_e('Sorry, nothing was found.'); ?></p>
<?php endif; ?>
