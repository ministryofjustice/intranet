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

$options = get_option('maintenance_options', [
  'maintenance_mode_status' => 0,
  'maintenance_mode_message' => '',
]);
$maintenance_mode = $options['maintenance_mode_status'] ?? false;

$title = $maintenance_mode ? 'Comments temporarily disabled' : 'Comment on this page';

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
  <h1 class="o-title o-title--subtitle"><?php echo $title; ?></h1>
    <?php
    if (is_user_logged_in() && !$maintenance_mode) { ?>
        <p>
            Your email address and comment will be shared with the author and Intranet Editors. See the <a href="<?php echo get_bloginfo('url'); ?>/privacy-notice/">Intranet Privacy Policy</a> for more information.
        </p>
        <?php
        // Display comment section - login and comment box text field
        comment_form($comments_args);
        ?>
      <p class="secondary-action">
        <a href="<?php echo get_bloginfo('url'); ?>/commenting-policy/">MoJ commenting policy</a><br/>
          <a href="<?php echo get_bloginfo('url'); ?>/delete-account/">Delete your comment history</a>
      </p>

        <?php
    } elseif (!$maintenance_mode) {
        echo '<p class="must-log-in" id="respond"><a href="' . wp_login_url(get_permalink()) . '">Login</a> or Register below to post a comment.</p>';
        get_template_part('src/components/c-register/view');
    }
    ?>
</section>
<!-- c-comment-form ends here -->
