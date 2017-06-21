<!-- c-comment-form starts here -->
<section class="c-comment-form">
  <h1>Comment on this page</h1>
  <p>Fill in your details below. We’ll then send you a link back to this page so you can start commenting.</p>
  <form action="">
    <?php
      form_builder('text', $prefix, 'Screen name (Will appear on screen)', 'your_name', null, null, 'Enter your name', null, true, null, null);
      form_builder('text', $prefix, 'Email address (Will not be shown with your comment)', 'your_email', null, null, 'Enter your email address', null, true, null, null);
    ?>
  </form>
</section>
<!-- c-comment-form ends here -->
