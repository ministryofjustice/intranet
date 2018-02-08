<?php
/***
 *
 * Comments
 *
 */
 // Exit if accessed directly
 if (! defined('ABSPATH')) {
     die();
 }

$comments = get_comments(
  [
  'post_id' => get_the_ID(),
  'status' => 'approve'
  ]);

// Getting the post meta assosiated with what the admin has set the comments on this page to. See admin->comments.php
// Meta values are 'comments_on', 'comments_off' and 'comments_closed'.
$post_meta = get_post_meta(get_the_ID());
$comments_disabled = $post_meta["comment_disabled_status"][0];
$comment_title = 'Comments';
?>

<!-- c-comments starts here -->
<section class="c-comments">

<?php
/***
 *
 * Switch statement displays code based on three scenarios, comments on, off and closed.
 *
 */
?>
<?php if (comments_open($post_id) === true && $comments_disabled === '0') : ?>

  <?php if (!get_comments_number()) : // Check in case comments gets switched on without any comments added.?>

    <h1 class="o-title o-title--subtitle"><?php echo $comment_title; ?></h1>
    <h3>Leave a comment</h3>

  <?php else: ?>

    <h1 class="o-title o-title--subtitle"><?php echo $comment_title; ?></h1>
    <ul class="commentlist">
    <?php
      wp_list_comments(
        [
        'reverse_top_level' => false,   // show newest at the top
        'reverse_children' => true,     // Setting this to true will display the children (reply level comments) with the most recent ones first
        'avatar_size' => false,
        'type'=> 'comment',
        'callback' => 'format_comment',
        ], $comments);
    ?>
    </ul>

  <?php endif; ?>

<?php elseif (comments_open($post_id) === true && $comments_disabled === 'comments_disabled') : ?>

  <?php if (!get_comments_number()) : //Check in case comments gets switched on without any comments added.?>

    <h1 class="o-title o-title--subtitle"><?php echo $comment_title; ?></h1>
    <h3>No comments have been left and comments have now been closed.</h3>

  <?php else: ?>

    <h1 class="o-title o-title--subtitle"><?php echo $comment_title; ?></h1>
    <ul class="commentlist">
    <p><span class="u-message u-message--warning">Comments are now closed</span></p>
    <?php
      wp_list_comments(
        [
        'reverse_top_level' => false,   // show newest at the top
        'reverse_children' => true,     // Setting this to true will display the children (reply level comments) with the most recent ones first
        'avatar_size' => false,
        'type'=> 'comment',
        'callback' => 'format_comment_closed',
        ], $comments);
    ?>
    </ul>
  <?php endif; ?>

<?php elseif (comments_open($post_id) === false) : ?>

  <?php echo ''; ?>

<?php else: ?>

  <?php
    echo 'Comments are not currently available.';
    endif; // End of if statement.
  ?>

</section>
<!-- c-comments ends here -->
