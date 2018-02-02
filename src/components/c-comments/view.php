
<?php
$post_id = get_the_ID();
$comments = get_comments(array(
  'post_id' => $post_id,
  'status' => 'approve'
));
?>
<!-- c-comments starts here -->
<section class="c-comments">
  <h1 class="o-title o-title--subtitle">Comments</h1>
  <p>There are no comments yet. Be the first to leave a comment.</p>
  <ul class="commentlist">
    <?php
      wp_list_comments(array(
        'reverse_top_level' => false, // show newest at the top
        'reverse_children' => true, // Setting this to true will display the children (reply level comments) with the most recent ones first
        'avatar_size' => false,
      ), $comments);
    ?>
  </ul>
</section>
<!-- c-comments ends here -->

