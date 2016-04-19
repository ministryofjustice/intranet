<?php

/*
 * Function creates post duplicate as a draft and redirects then to the edit post screen
 */
function dw_fork_post_as_draft(){
  global $wpdb;
  if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'dw_fork_post_as_draft' == $_REQUEST['action'] ) ) ) {
    wp_die('No post to fork has been supplied!');
  }

  /*
   * get the original post id
   */
  $post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
  /*
   * and all the original post data then
   */
  $post = get_post( $post_id );

  /*
   * if you don't want current user to be the new post author,
   * then change next couple of lines to this: $new_post_author = $post->post_author;
   */
  $current_user = wp_get_current_user();
  $new_post_author = $current_user->ID;

  /*
   * if post data exists, create the post duplicate
   */
  if (isset( $post ) && $post != null) {

    /*
     * new post data array
     */
    $args = array(
        'comment_status' => $post->comment_status,
        'ping_status'    => $post->ping_status,
        'post_author'    => $new_post_author,
        'post_content'   => $post->post_content,
        'post_excerpt'   => $post->post_excerpt,
        'post_name'      => $post->post_name,
        'post_parent'    => $post->post_parent,
        'post_password'  => $post->post_password,
        'post_status'    => 'draft',
        'post_title'     => $post->post_title,
        'post_type'      => $post->post_type,
        'to_ping'        => $post->to_ping,
        'menu_order'     => $post->menu_order
    );

    /*
     * insert the post by wp_insert_post() function
     */
    $new_post_id = wp_insert_post($args);

    /*
     * get all current post terms ad set them to the new post draft
     */
    $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
    foreach ($taxonomies as $taxonomy) {

      if ($taxonomy != 'agency' && $taxonomy != 'author') {
        $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
        wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
      }
    }

    $context = Agency_Context::get_agency_context();

    /*
     * Opt out Agency on original post
     */
    $post_agencies = wp_get_object_terms($post_id, 'agency', array('fields' => 'slugs'));

    if (in_array($context,$post_agencies) && in_array('hq',$post_agencies)) {
      $post_agencies = array_diff($post_agencies, array($context));
      wp_set_object_terms($post_id, $post_agencies, 'agency', false);
    }

    /*
     * Set Agency
     */
    wp_set_object_terms($new_post_id, $context, 'agency', false);

    /*
     * duplicate all post meta just in two SQL queries
     */
    $post_meta_ary = get_post_meta($post_id);

    if (count($post_meta_ary)!=0) {

      foreach ($post_meta_ary as $key => $post_meta) {

        if (is_array($post_meta) && count($post_meta) > 0) {
          add_post_meta($new_post_id, $key, $post_meta[0]);
        }

      }

    }

    /*
     * Add post id of original post
     */

    add_post_meta($new_post_id, 'fork_from_post_id', $post_id);

    /*
     * finally, redirect to the edit post screen for the new draft
     */
    wp_redirect(admin_url( 'post.php?action=edit&post=' . $new_post_id ));
    exit;
  } else {
    wp_die('Post creation failed, could not find original post: ' . $post_id);
  }
}
add_action('admin_action_dw_fork_post_as_draft', 'dw_fork_post_as_draft');

/*
 * Add the duplicate link to action list for post_row_actions
 */
function dw_fork_post_link( $actions, $post ) {

  $context = Agency_Context::get_agency_context();

  if (current_user_can('edit_posts') && $post->post_status == 'publish' && $context != 'hq') {
    $actions['duplicate'] = '<a href="admin.php?action=dw_fork_post_as_draft&amp;post=' . $post->ID . '" title="Fork this item" rel="permalink">Fork</a>';
  }
  return $actions;
}

add_filter('post_row_actions', 'dw_fork_post_link', 10, 2);
add_filter('page_row_actions', 'dw_fork_post_link', 10, 2);