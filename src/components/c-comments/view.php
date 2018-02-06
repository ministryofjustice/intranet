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

$post_id = get_the_ID();
$comments = get_comments(array(
  'post_id' => $post_id,
  'status' => 'approve'
));

?>

<!-- c-comments starts here -->
<section class="c-comments">
  <?php
    if (comments_open() && get_comments_number()) {
  ?>
        <h1 class="o-title o-title--subtitle">Comments</h1>
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
  <?php

    } else {
        return;
    }
  ?>

</section>
<!-- c-comments ends here -->
