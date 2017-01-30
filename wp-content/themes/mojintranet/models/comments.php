<?php if (!defined('ABSPATH')) die();

class Comments_model extends MVC_model {
  private $author_cache;

  public function read($params) {
    $params['last_comment_id'] = get_array_value($params, 'last_comment_id', null);
    $params['per_page'] = get_array_value($params, 'per_page', 0);
    $data = $this->get_raw($params['post_id'], $params['root_comment_id'], $params['last_comment_id'], $params['per_page']);
    $data = $this->format_data($data);
    return $data;
  }

  public function update($post_id, $comment_content, $parent_id, $root_comment_id, $nonce) {
    //if(wp_verify_nonce( $nonce, 'dw_comment' )) {
      return $this->add_comment($post_id, $comment_content, $parent_id, $root_comment_id);
    //}
  }

  private function get_raw($post_id, $root_comment_id = 0, $last_comment_id, $per_page) {
    // Get comments
    if((int) $root_comment_id === 0) {
      $returned_comments = $this->get_top_level_comments($post_id, $last_comment_id, $per_page);
    } else {
      $returned_comments = $this->get_replies($root_comment_id, $per_page);
    }

    // Get total comments and number of comments retrieved
    $data['total_comments'] = (int) $this->get_raw_results(true);
    $data['retrieved_comments'] = (int) count($returned_comments);

    $data['raw'] = $returned_comments;

    // Return merged data
    return $data;
  }

  private function get_top_level_comments($post_id, $last_comment_id, $per_page) {
    $this->last_comment_id = $last_comment_id;
    $options['post_id'] = $post_id;
    $options['parent'] = 0;

    $options['number'] = $per_page;

    if ( $last_comment_id ) {
      add_filter('comments_clauses', array($this, 'limit_comments_by_id'));
    }

    $this->options = $this->initialise_options($options);

    $data = $this->get_raw_results();

    if( $last_comment_id ) {
      remove_filter('comments_clauses', array($this, 'limit_comments_by_id'));
    }

    return $data;
  }

  private function initialise_options($options) {
    $default = array(
      'status' => 'approve',
      'number' => 10,
      'orderby' => 'comment_ID',
      'order' => 'DESC'
    );

    foreach($options as $key=>$value) {
      $default[$key] = $value;
    }

    return $default;
  }

  private function get_raw_results($return_count = false) {
    $args = $this->options;
    $results_query = new WP_Comment_Query;
    $args['count'] = $return_count;
    $results = $results_query->query($args);

    return $results;
  }

  private function format_data($data) {
    $comments = array();

    $this->author_cache = array();

    foreach ($data['raw'] as $comment) {
      $this->update_user_cache($comment->comment_ID, $comment->user_id);

      $root_comment_id = get_comment_meta( $comment->comment_ID, 'root_comment_id', true );

      $current_comment = $this->format_comment($comment);

      $replies = $this->get_replies($comment->comment_ID, 2);
      $current_comment['total_replies'] = 0;
      foreach ($replies as $reply) {
        $current_comment['total_replies'] =+ $this->get_replies($comment->comment_ID, 0, true);
        $current_comment['replies'][] = $this->format_comment($reply);
      }

      $comments[] = $current_comment;

    }

    unset($data['raw']);
    $data['comments'] = $comments;

    return $data;
  }

  private function get_replies($top_level_comment_id, $per_page, $return_count = false) {
    $options['meta_query'] = array(
      array(
        'key'     => 'root_comment_id',
        'value'   => $top_level_comment_id,
        'compare' => 'IN'
      )
    );
    $options['orderby'] = 'comment_date_gmt';
    $options['order'] = 'DESC';

    if($per_page) {
      $options['number'] = $per_page;
    }

    $this->options = $this->initialise_options($options);

    $data = $this->get_raw_results($return_count);

    if(is_array($data)) {
      $data = array_reverse($data);
    }

    return $data;
  }

  private function update_user_cache($comment_id, $author_id) {
    $user = get_user_by( 'id', $author_id );
    $this->author_cache[$comment_id] = $user->display_name;
  }

  private function format_comment($comment) {
    $hidden_comment = get_comment_meta( $comment->comment_ID, 'hidden_comment', true );
    $like_count = get_comment_meta( $comment->comment_ID, 'dw_inc_likes', true );
    $parent_author = $comment->comment_parent ? get_comment_author($comment->comment_parent) : '';

    return array(
      'id' => (int) $comment->comment_ID,
      'date_posted' => $comment->comment_date,
      'author_name' => $comment->comment_author,
      'comment' => $comment->comment_content,
      'likes' => (int) $like_count,
      'in_reply_to_id' => (int) $comment->comment_parent,
      'in_reply_to_author' => $parent_author,
      'hidden_comment' => (int) $hidden_comment ?: 0,
      'total_replies' => 0,
      'replies' => array()
    );
  }

  private function add_comment($post_id, $comment_content, $parent_id = 0, $root_comment_id = 0) {
    $current_user = wp_get_current_user();

    if ($current_user) {
      $data = array(
        'comment_content' => $comment_content,
        'comment_post_ID' => $post_id,
        'comment_parent' => $parent_id,
        'comment_approved' => 1,
        'comment_author' => $current_user->display_name,
        'comment_author_email' => $current_user->user_email, //fixed value - can be dynamic,
        'comment_author_url' => null,
        'user_id' => $current_user->ID,
        'comment_meta' => array(
          'root_comment_id' => $root_comment_id
        ),
      );

      $new_comment_id = wp_new_comment($data);
      return $this->format_comment(get_comment($new_comment_id));
    } else {
      return false;
    }
  }

  public function limit_comments_by_id($pieces) {
    $pieces['where'] = $pieces['where'] . " AND comment_ID<" . $this->last_comment_id;
    return $pieces;
  }
}
