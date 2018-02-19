<?php
  $loginPrefix = 'cfl'
  $commentPrefix = 'cfc'
?>
<!-- c-comment-form starts here -->
<section class="c-comment-form">
  <h1 class="o-title o-title--subtitle">Comment on this page</h1>
  <!-- If !LoggedIn -->
  <p>Fill in your details below. We’ll then send you a link back to this page so you can start commenting.</p>

  <form action="" class="c-comment-form__login">
    <?php
      form_builder('text', $loginPrefix, 'Screen name (Will appear on screen)', 'your_name', null, null, 'Enter your name', null, true, null, null);
      form_builder('text', $loginPrefix, 'Email address (Will not be shown with your comment)', 'your_email', null, null, 'Enter your email address', null, true, null, null);
    ?>
    <button class="o-button">Get link</button>

    <div class="o-clear-space">
      <a href="">MoJ Commenting Policy</a>
    </div>

  </form>

  <!-- If LoggedIn -->
  <p>You're posting as Alex Foxleigh | <a href="">Not you?</a></p>
  <form action="" class="c-comment-form__comment">
    <?php
      form_builder('textarea', $commentPrefix, 'Enter your comment', 'your_comment', null, null, 'Your comment goes here...', null, true, null, null);
    ?>
    <button class="o-button">Add Comment</button>
    <button class="o-text-button">Cancel</button>
  </form>
</section>
<!-- c-comment-form ends here -->
