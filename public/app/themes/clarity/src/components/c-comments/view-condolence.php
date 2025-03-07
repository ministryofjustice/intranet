<?php
/***
 *
 * Comments
 */
$comments = get_comments(
    [
        'post_id' => get_the_ID(),
        'status'  => 'approve',
    ]
);


// Get core WP meta
$post_id             = get_the_ID();
$post_meta           = get_post_meta(get_the_ID());
$post                = get_post();
$post_comment_status = $post->comment_status;

if (isset($post_meta['comment_disabled_status'][0])) {
    $comments_disabled = sanitize_text_field($post_meta['comment_disabled_status'][0]);
} else {
    $comments_disabled = '';
}
// Getting the post meta assosiated with what the admin has set the comments on this page to. See admin->comments.php
// $comments_disabled = "" ? $comments_disabled === '0' : $comments_disabled;
// Don't want to repeat ourselves - DRY
$comment_title = 'Messages';
?>

<!-- c-comments starts here -->
<section class="c-comments">

<?php

/***
 *
 * If statement displays code based on three scenarios, comments on, off and closed.
 */
?>

<?php
if (comments_open($post_id) === true && $comments_disabled === 'comments_disabled') {
    ?>

    <?php if (! get_comments_number()) : // Check in case comments gets switched on without any comments added. ?>
      <h1 class="o-title o-title--subtitle"><?= $comment_title ?></h1>
      <h3>No messages have been left and messages have now been closed.</h3>

    <?php else : ?>
      <h1 class="o-title o-title--subtitle"><?= $comment_title ?></h1>
      <ul class="commentlist">
      <p><span class="u-message u-message--warning">Messages are now closed</span></p>
        <?php
        wp_list_comments(
            [
                'reverse_top_level' => false,   // show newest at the top
                'reverse_children'  => true,     // Setting this to true will display the children (reply level comments) with the most recent ones first
                'avatar_size'       => false,
                'type'              => 'comment',
                'callback'          => 'format_comment_closed',
            ],
            $comments
        );
        ?>
      </ul>
    <?php endif; ?>
    <?php
} elseif (comments_open($post_id) === true || $post_comment_status === 'open' && $comments_disabled === '0') {
    ?>
    <?php if (! get_comments_number()) : // Check in case comments gets switched on without any comments added. ?>
        <?php get_template_part('src/components/c-comment-form/view', 'condolence'); ?>
    <!-- <h1 class="o-title o-title--subtitle"><?= $comment_title ?></h1> -->
    <!-- <h3>Leave a comment</h3> -->

    <?php else : ?>
        <?php get_template_part('src/components/c-comment-form/view', 'condolence'); ?>

    <h1 class="o-title o-title--subtitle"><?= $comment_title ?></h1>
    <ul class="commentlist">
        <?php
        wp_list_comments(
            [
            'reverse_top_level' => false,   // show newest at the top
            'reverse_children'  => true,     // Setting this to true will display the children (reply level comments) with the most recent ones first
            'avatar_size'       => false,
            'type'              => 'comment',
            'callback'          => 'format_comment',
            ],
            $comments
        );
        ?>
    </ul>
    <?php endif;
} elseif (! comments_open($post_id) && $comments_disabled === '0') {
    ?>
    <?= ''; ?>
    <?php
} elseif (! comments_open($post_id) && $comments_disabled === 'comments_disabled') {
    ?>
    <?= ''; ?>
    <?php
} else {
    ?>
    <?= '<!-- Comment settings have not yet been set on this page. -->'; ?>
    <?php
}
?>

</section>
<!-- c-comments ends here -->
