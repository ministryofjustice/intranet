<?php
  /***
   *
   * Comment form - display of login form and comment box
   * Also see inc/comments.php
   */

  $commenter = wp_get_current_commenter();
  $req       = get_option('require_name_email');
  $aria_req  = ( $req ? " aria-required='true'" : '' );
  $post_meta = get_post_meta(get_the_ID());

if (isset($post_meta['comment_disabled_status'][0])) {
    $comments_disabled = sanitize_text_field($post_meta['comment_disabled_status'][0]);
}

  // Login form - required author name and email
  $fields = [
      'author' => '<p class="comment-form-author">' . '<label for="author">' . __('Name') . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
          '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' /></p>',
      'email'  => '<p class="comment-form-email"><label for="email">' . __('Email') . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
          '<input id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' /></p>',
  ];

  $comments_args = [
      // Login form - appears if you are not logged in.
      'fields'        => $fields,
      'title_reply'   => '',

      // Comment form - appears once you are logged in
      'label_submit'  => 'Add comment',
      'comment_field' => '<p class="comment-form-comment">
      <textarea required id="comment" name="comment" placeholder="' . esc_attr__('Enter your comment here...', 'text-domain') . '" cols="45" rows="8" aria-required="true"></textarea>
      </p>',
  ];
    ?>

<!-- c-comment-form starts here -->
<section class="c-comment-form">
  <h1 class="o-title o-title--subtitle">Comment on this page</h1>
    <?php
    if (is_user_logged_in()) {
        // Display comment section - login and comment box text field
        comment_form($comments_args);
        ?>
      <p class="secondary-action">
        <a href="https://intranet.justice.gov.uk/commenting-policy/">MoJ commenting policy</a>
      </p>
        <?php
    } else {
        echo '<p class="must-log-in" id="respond"><a href="' . wp_login_url(get_permalink()) . '">Login</a> or Register below to post a comment.</p>';
        get_template_part('src/components/c-register/view');
    }
    ?>
</section>
<!-- c-comment-form ends here -->
