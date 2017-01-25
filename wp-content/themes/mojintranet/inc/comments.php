<?php

  class EnhanceComments {

    public function __construct() {
      $this->add_hooks();
    }

    private function add_hooks() {
      // Actions
      add_action ('admin_action_hidecomment',array($this,'hide_comment'),10,2);
      add_action ('admin_action_unhidecomment',array($this,'unhide_comment'),10,2);

      // Filters
      add_filter('comment_row_actions',array($this,'add_comment_hide_link'),10,2);
      add_filter('manage_edit-comments_columns', array($this,'add_comment_hidden_column'),10,2);
      add_filter('manage_comments_custom_column', array($this,'display_comment_hidden_column'),10,2);
    }

    public function add_comment_hide_link($actions, $comment) {

      $url = "admin.php?c=$comment->comment_ID"; 

      $hide_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "hide-comment_$comment->comment_ID" ) );

      $hide_url = esc_url( $url . "&action=hidecomment&$hide_nonce" );
      $unhide_url = esc_url( $url . "&action=unhidecomment&$hide_nonce" );

      $hidden_comment = get_comment_meta( $comment->comment_ID, 'hidden_comment', true );

      // Hide Trash action for non-admins
      if ( !current_user_can( 'manage_options' ) ) {
        unset($actions['trash']);
      }

      if(!$hidden_comment) {
        $actions['hide'] = "<a href='$hide_url' data-wp-lists='hide:the-comment-list:comment-$comment->comment_ID::hide=1' class='hide vim-d vim-destructive' title='" . esc_attr__( 'Hide this comment on the site' ) . "'>" . _x( 'Hide', 'verb' ) . '</a>';
      } else {
        $actions['unhide'] = "<a href='$unhide_url' data-wp-lists='hide:the-comment-list:comment-$comment->comment_ID::hide=1' class='hide vim-d vim-destructive' title='" . esc_attr__( 'Unhide this comment on the site' ) . "'>" . _x( 'Unhide', 'verb' ) . '</a>';
      }

      return $actions;
    }

    public function hide_comment($post_id) {
      $comment_id = $_GET['c'];
      $user_id = get_current_user_id();

      // Set hidden_comment meta to current user ID
      update_comment_meta( $comment_id, 'hidden_comment', $user_id );

      // Return to referring page
      wp_redirect( $_SERVER['HTTP_REFERER'] );
      exit;
    }

    public function unhide_comment($post_id) {
      $comment_id = $_GET['c'];

      // Delete 'hidden_comment' flag
      delete_comment_meta( $comment_id, 'hidden_comment' );

      // Return to referring page
      wp_redirect( $_SERVER['HTTP_REFERER'] );
      exit;
    }

    public function add_comment_hidden_column($columns) {
      $offset = array_search('response', array_keys($columns));
      return array_merge(
        array_slice($columns, 0, $offset),
        array('comment_hidden' => __( 'Hidden?' )),
        array_slice($columns, $offset, null)
      );
    }

    public function display_comment_hidden_column($column, $comment_id) {
      switch ( $column ) {
        case 'comment_hidden':
          if (get_comment_meta($comment_id, 'hidden_comment')) {
            echo "<span class='dashicons dashicons-yes'></span>";
          } else {
            echo "<span class='dashicons dashicons-no'></span>";
          }
          break;
      }
    }

  }

  new EnhanceComments;

function dw_add_new_discussion_meta_box() {
  remove_meta_box('commentstatusdiv', 'post', 'normal');
  add_meta_box('commentstatusdiv', __('Discussion'), 'dw_comment_status_meta_box', 'post', 'normal', 'high');
}
add_action('add_meta_boxes_post',  'dw_add_new_discussion_meta_box');

function dw_comment_status_meta_box($post) {
  global $pagenow;
  if(in_array($pagenow, array('post-new.php'))) {
    $comments_on = 1;
  }
  else {
    $comments_on = get_post_meta($post->ID, 'dw_comments_on', true);
  }
  ?>
  <input name="advanced_view" type="hidden" value="1" />
  <p class="meta-options">
    <label for="comments_on" class="selectit"><input name="comments_on" type="checkbox" id="comments_on" value="1"  <?php checked($comments_on, 1); ?> /> <?php _e('Comments on') ?></label>
    <label for="comment_status" class="selectit comment_status_option <?php if($comments_on != 1) { echo "status_hidden"; }?>"><br/><input name="comment_status" type="checkbox" id="comment_status" value="open"  <?php checked($post->comment_status, 'open'); ?> /> <?php _e('Comments open') ?></label><br />
    <label for="ping_status" class="selectit ping_status"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($post->ping_status, 'open'); ?> /> <?php
      printf(
      /* translators: %s: Codex URL */
          __( 'Allow <a href="%s">trackbacks and pingbacks</a> on this page.' ),
          __( 'https://codex.wordpress.org/Introduction_to_Blogging#Managing_Comments' ) );
      ?></label>
    <?php
    /**
     * Fires at the end of the Discussion meta box on the post editing screen.
     *
     * @since 3.1.0
     *
     * @param WP_Post $post WP_Post object of the current post.
     */
    do_action( 'post_comment_status_meta_box-options', $post );
    ?>
  </p>
  <?php
}

function dw_set_comments_on_meta($post_id) {
  $post_type = get_post_type($post_id);

  if ("post" != $post_type) return;

  if (isset($_POST['comments_on'])) {
    update_post_meta($post_id, 'dw_comments_on', 1);
  }
  else {
    update_post_meta($post_id, 'dw_comments_on', 0);
  }
}
add_action('save_post', 'dw_set_comments_on_meta', 10, 1);

function dw_discussion_options_script($hook) {
  global $post;

  if ($hook == 'post-new.php' || $hook == 'post.php') {
    if ('post' === $post->post_type) {
      wp_enqueue_script('discussion-options', get_stylesheet_directory_uri().'/admin/js/discussion-options.js');
    }
  }
}
add_action('admin_enqueue_scripts', 'dw_discussion_options_script', 10, 1);
