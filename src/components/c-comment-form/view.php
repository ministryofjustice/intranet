<?php 
  $commenter = wp_get_current_commenter();
  $req = get_option( 'require_name_email' );
  $aria_req = ( $req ? " aria-required='true'" : '' );

  $post_meta = get_post_meta(get_the_ID());
  if (isset($post_meta["comment_disabled_status"][0])) {
    $comments_disabled = $post_meta["comment_disabled_status"][0];
  } else {
    $comments_disabled = '';
  }

  $fields =  array(
      'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
          '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
      'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
          '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
  );
  
  $comments_args = array(
      'fields' =>  $fields,
      'title_reply'=>'',
      'label_submit' => 'Add comment'
  );
?>
<?php 
  
  if($comments_disabled === '0'){

  }elseif($comments_disabled === 'comments_disabled'){

  }else{
  ?>
  <!-- c-comment-form starts here -->
  <section class="c-comment-form">
    <h1 class="o-title o-title--subtitle">Comment on this page</h1>
    <?php 
    if (is_user_logged_in()){
      comment_form($comments_args);
      ?> 
      <p class="secondary-action">
        <a href="https://intranet.justice.gov.uk/commenting-policy/">MoJ commenting policy</a>
      </p>
      <?php
    }else{
      echo '<p class="must-log-in" id="respond"><a href="'.wp_login_url(get_permalink()).'">Login</a> or Register below to post a comment.</p>';
      get_template_part('src/components/c-register/view'); 
    }
    ?>
  </section>
<!-- c-comment-form ends here -->
<?php
  }
  
?>
