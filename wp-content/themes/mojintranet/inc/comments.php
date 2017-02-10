<?php

  class HideComments {

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

      $is_hidden = get_comment_meta( $comment->comment_ID, 'is_hidden', true );

      // Hide Trash action for non-admins
      if ( !current_user_can( 'manage_options' ) ) {
        unset($actions['trash']);
        unset($actions['spam']);
        unset($actions['approve']);
        unset($actions['unapprove']);

      }

      if(!$is_hidden) {
        $actions['hide'] = "<a href='$hide_url' data-wp-lists='hide:the-comment-list:comment-$comment->comment_ID::hide=1' class='hide vim-d vim-destructive' title='" . esc_attr__( 'Hide this comment on the site' ) . "'>" . _x( 'Hide', 'verb' ) . '</a>';
      } else {
        $actions['unhide'] = "<a href='$unhide_url' data-wp-lists='hide:the-comment-list:comment-$comment->comment_ID::hide=1' class='hide vim-d vim-destructive' title='" . esc_attr__( 'Unhide this comment on the site' ) . "'>" . _x( 'Unhide', 'verb' ) . '</a>';
      }

      return $actions;
    }

    public function hide_comment($post_id) {
      $comment_id = $_GET['c'];

      update_comment_meta( $comment_id, 'is_hidden', true);

      // Return to referring page
      wp_redirect( $_SERVER['HTTP_REFERER'] );
      exit;
    }

    public function unhide_comment($post_id) {
      $comment_id = $_GET['c'];

      // Delete 'hidden_comment' flag
      delete_comment_meta( $comment_id, 'is_hidden' );

      // Return to referring page
      wp_redirect( $_SERVER['HTTP_REFERER'] );
      exit;
    }

    public function add_comment_hidden_column($columns) {
      $offset = array_search('response', array_keys($columns));
      return array_merge(
        array_slice($columns, 0, $offset),
        array('comment_hidden' => __( 'Hidden' )),
        array_slice($columns, $offset, null)
      );
    }

    public function display_comment_hidden_column($column, $comment_id) {
      switch ( $column ) {
        case 'comment_hidden':
          if (get_comment_meta($comment_id, 'is_hidden')) {
            echo "Hidden";
          } else {
            echo "-";
          }
          break;
      }
    }

  }

  new HideComments;


/**
 * Removes the default discussion meta box and replaces it with a custom meta box
 *
 */
function dw_add_new_discussion_meta_box() {
  remove_meta_box('commentstatusdiv', 'post', 'normal');
  remove_meta_box('commentstatusdiv', 'page', 'normal');
  add_meta_box('commentstatusdiv', __('Discussion'), 'dw_discussion_meta_box', ['post', 'page'], 'normal', 'high');
}
add_action('add_meta_boxes_post',  'dw_add_new_discussion_meta_box');
add_action('add_meta_boxes_page',  'dw_add_new_discussion_meta_box');

/**
 * Custom Discussion Meta box
 * @param  object $post Post
 */
function dw_discussion_meta_box($post) {
  global $pagenow;
  global $post_type;

  if(in_array($pagenow, array('post-new.php')) && $post_type == 'post' ) {
    $comments_on = 1;
  }
  else {
    $comments_on = get_post_meta($post->ID, 'dw_comments_on', true);
  }
  ?>
  <input name="advanced_view" type="hidden" value="1" />
  <p class="meta-options">
    <label for="comments_on" class="selectit"><input name="comments_on" type="checkbox" id="comments_on" value="1"  <?php checked($comments_on, 1); ?> /> <?php _e('Show the comments section') ?></label>
    <label for="comment_status" class="selectit comment_status_option <?php if($comments_on != 1) { echo "status_hidden"; }?>"><br/><input name="comment_status" type="checkbox" id="comment_status" value="open"  <?php checked($post->comment_status, 'open'); ?> /> <?php _e('Allow people to add new comments') ?></label><br />
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

/**
 * Updates the 'Comments on' Meta data
 * @param  int $post_id Post ID
 *
 */
function dw_set_comments_on_meta($post_id) {
  $post_type = get_post_type($post_id);

  if (!in_array($post_type, ['post', 'page', 'revision'])) return;

  if (isset($_POST['comments_on'])) {
    update_metadata('post', $post_id, 'dw_comments_on', 1);
  }
  else {
    update_metadata('post', $post_id, 'dw_comments_on', 0);
  }
}
add_action('save_post', 'dw_set_comments_on_meta', 10, 1);

/**
 * Sets Comment Status even if it has not be sent in the $_POST
 * @param  array $data Post data to be inserted
 * @param  array $postarr Post Data
 * @return array Altered data to be inserted
 */
function dw_save_comment_status($data , $postarr) {
  global  $post;

  if (!empty($_POST["comment_status"])) {

    $data["comment_status"] = $_POST["comment_status"];

  }
  else {
    $data["comment_status"] = 'closed';
  }
  return $data;
}
add_filter('wp_insert_post_data', 'dw_save_comment_status', '99', 2);

/**
 * Adds script used by the Custom Discussion Meta Box
 * @param  string $hook The page that is being viewed
 *
 */
function dw_discussion_options_script($hook) {
  global $post;

  if ($hook == 'post-new.php' || $hook == 'post.php') {
    if (in_array($post->post_type, ['post', 'page'])) {
      wp_enqueue_script('discussion-options', get_stylesheet_directory_uri().'/admin/js/discussion-options.js');
    }
  }
}
add_action('admin_enqueue_scripts', 'dw_discussion_options_script', 10, 1);

/**
 * Remove duplicate comment check
 *
 */
add_filter('duplicate_comment_id', '__return_false');

/**
 * Add Hidden Marker to the comments listing shown on the edit post page
 * @param  string $comment_text Comment text
 * @param  object $comment Comment Object
 * @param  array $args Additional Comment Arguments
 * @return string Comment Text
 */
function dw_add_hidden_comment_marker($comment_text, $comment, $args) {
  if( doing_action( 'wp_ajax_get-comments' ) ) {
    $is_hidden = get_comment_meta( $comment->comment_ID, 'is_hidden', true );

    if($is_hidden) {
      echo '<div class="hidden-comment">Hidden</div>';
    }
  }

  return $comment_text;
}
add_filter( 'comment_text', 'dw_add_hidden_comment_marker', 10, 3 );

/**
 * Adds the root comment id meta if it is not already set.
 * @param  array $comment_data Comment data to be inserted
 * @return array Comment data
 */
function dw_add_root_comment($comment_data) {
  if (is_numeric($comment_data['comment_parent']) &&  $comment_data['comment_parent'] > 0) {
      if (empty($comment_data['comment_meta']) || empty($comment_data['comment_meta']['root_comment_id'])) {
          $root_comment_id = $comment_data['comment_parent'];
          $top_parent = false;
          while ($top_parent == false) {
            $parent_comment = get_comment($root_comment_id);

            if($parent_comment->comment_parent == 0) {
              $top_parent = true;
            }
            else {
              $root_comment_id = $parent_comment->comment_parent;
            }
          }

          $comment_data['comment_meta']['root_comment_id'] = $root_comment_id;
      }
  }

  return $comment_data;
}
add_filter('preprocess_comment','dw_add_root_comment',10,1);


