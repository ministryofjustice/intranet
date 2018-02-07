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
$comment_status = $post_meta["discussion_meta_box_value"][0];
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
<?php switch ($comment_status) {

  // First case. Comments on. Comments appear on page.
  case 'comments_on': ?>

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

<?php break; ?>

<?php

  // Next case. Comments off. No comments to appear on page.
  case 'comments_off':
  return;
?>

<?php break; ?>

<?php

  // Third case. Comments closed. Comments appear on page but users cannot add further comments.
  case 'comments_closed': ?>

  <?php if (!get_comments_number()) : //Check in case comments gets switched on without any comments added.?>

    <h1 class="o-title o-title--subtitle"><?php echo $comment_title; ?></h1>
    <h3>No comments have been left and comments have now been closed.</h3>

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
        'callback' => 'format_comment_closed',
        ], $comments);
    ?>
    </ul>

  <?php endif; ?>

<?php break; ?>

<?php
  // Final case. In case there is some error, we have a fallback message.
  default:
    echo 'There are no comments available.';

  } // End of switch statement.
?>

</section>
<!-- c-comments ends here -->
