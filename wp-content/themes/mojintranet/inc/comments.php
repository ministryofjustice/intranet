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

?>