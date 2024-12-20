<?php

if (! defined('ABSPATH')) {
    die();
}

/*
* Single event page
*/
if (have_posts()) :
    while (have_posts()) :
        the_post();
        $post_meta = get_post_meta(get_the_ID());        
        $options = get_option('maintenance_options', [
            'maintenance_mode_status' => 0,
            'maintenance_mode_message' => '',
        ]);
        $maintenance_mode = $options['maintenance_mode_status'] ?? false;
        ?>
<!-- c-share-post starts here -->
<section class="c-share-post">
  <ul class="c-share-post__link">
        <?php
        if (comments_open() == '1') {
            ?>
        <li class="u-icon u-icon--chat_bubble"><span><?= get_comments_number() ?></span> </li>
            <?php
        }
        ?>

    <?php
        if (!$maintenance_mode) {
            ?>
              <li><?= do_shortcode('[likebutton]') ?><li>
            <?php
        }
    ?>

      <span class="u-icon u-icon--redo2"></span>
      <a href="mailto:?subject=<?= get_the_title() ?>&body=A colleague thought you would be interested in this intranet article <?= get_permalink() ?>" class="share-email">Share this post by email</a>
    </li>
  </ul>
</section>

    <?php endwhile;
else : ?>
    <p><?php esc_html_e('Sorry, nothing was found.'); ?></p>
<?php endif; ?>
<!-- c-share-post ends here -->
