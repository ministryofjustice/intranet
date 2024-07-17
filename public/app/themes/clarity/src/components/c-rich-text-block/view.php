<?php
if (have_posts()) :
    while (have_posts()) :
        the_post();
        do_action('before_rich_text_block');
        ?>

<!-- c-rich-text-block starts here -->
<section class="c-rich-text-block">
    <?= apply_filters( 'the_content', get_sub_field(the_content())) ?>
</section>
<!-- c-rich-text-block ends here -->

    <?php endwhile;
else : ?>
<p><?php esc_html_e('Sorry, nothing was found.'); ?></p>
<?php endif; ?>
