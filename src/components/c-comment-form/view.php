<?php
  $prefix = 'cfp'
?>
<!-- c-comment-form starts here -->
<section class="c-comment-form">
  <h1>Comment on this page</h1>
  <!-- If !LoggedIn -->
  <p>Fill in your details below. We’ll then send you a link back to this page so you can start commenting.</p>

  <form action="" class="c-comment-form__login">
    <?php
      form_builder('text', $prefix, 'Screen name (Will appear on screen)', 'your_name', null, null, 'Enter your name', null, true, null, null);
      form_builder('text', $prefix, 'Email address (Will not be shown with your comment)', 'your_email', null, null, 'Enter your email address', null, true, null, null);
    ?>
    <button>Get link</button>
    <a href="">MoJ Commenting Policy</a>
  </form>

  <!-- If LoggedIn -->
  <p>You're posting as Alex Foxleigh | <a href="">Not you?</a></p>
  <form action="" class="c-comment-form__comment">
    <?php
      form_builder('textarea', $prefix, 'Enter your comment', 'your_comment', null, null, 'Your comment goes here...', null, true, null, null);
    ?>
    <button>Add Comment</button>
    <button>Cancel</button>
  </form>
</section>
<!-- c-comment-form ends here -->
