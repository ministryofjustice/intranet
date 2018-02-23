<?php

/*
 * Function creates post duplicate as a draft and redirects then to the edit post screen
 */
function dw_fork_post_as_draft() {
  $post_was_set = isset( $_REQUEST['post']);
  $correct_action_was_set = isset($_REQUEST['action']) && 'dw_fork_post_as_draft' == $_REQUEST['action'];

  if (!$post_was_set || !$correct_action_was_set) {
    wp_die('No post to fork has been supplied!');
  }

  $post_id = $_REQUEST['post'];
  $post = get_post($post_id);
  $new_post_author = get_current_user_id();

  $context = Agency_Context::get_agency_context();

  /*
   * If post data exists, create the post duplicate
   */
  if (isset($post) && $post != null) {

    /*
     * New post data array
     */
    $args = array(
        'comment_status' => $post->comment_status,
        'ping_status'    => $post->ping_status,
        'post_author'    => $new_post_author,
        'post_content'   => $post->post_content,
        'post_excerpt'   => $post->post_excerpt,
        'post_name'      => $context .'-'. $post->post_name,
        'post_parent'    => $post->post_parent,
        'post_password'  => $post->post_password,
        'post_status'    => 'draft',
        'post_title'     => $post->post_title,
        'post_type'      => $post->post_type,
        'to_ping'        => $post->to_ping,
        'menu_order'     => $post->menu_order
    );

    /*
     * Insert the post by wp_insert_post() function
     */
    $new_post_id = wp_insert_post($args);

    /* Set filtered content */
    global $wpdb;
    $wpdb->query( $wpdb->prepare(
        "UPDATE $wpdb->posts SET `post_content_filtered` = '%s' WHERE `ID` = %d", array($post->post_content_filtered, $new_post_id)
    ) );
    /*
     * Get all current post terms and set them to the new post draft
     */
    $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
    foreach ($taxonomies as $taxonomy) {

      if ($taxonomy != 'agency' && $taxonomy != 'author') {
        $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
        wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
      }
    }



    /*
     * Opt out Agency from original post - excluded on HQ context
     */
    $post_agencies = wp_get_object_terms($post_id, 'agency', array('fields' => 'slugs'));

    if ($context != 'hq' && in_array($context, $post_agencies) && in_array('hq', $post_agencies)) {
      $post_agencies = array_diff($post_agencies, array($context));
      wp_set_object_terms($post_id, $post_agencies, 'agency', false);
    }

    /*
     * Set Agency for new post
     */
    wp_set_object_terms($new_post_id, $context, 'agency', false);

    /*
     * Duplicate post meta
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
     * Redirect to the edit post screen for the new draft
     */
    wp_redirect(admin_url( 'post.php?action=edit&post=' . $new_post_id ));
    exit;
  } else {
    wp_die('Post creation failed, could not find original post: ' . $post_id);
  }
}
add_action('admin_action_dw_fork_post_as_draft', 'dw_fork_post_as_draft');

/*
 * Add the fork link to action list for post_row_actions
 */
function dw_fork_post_link( $actions, $post ) {

  $context = Agency_Context::get_agency_context();
  $current_template = get_post_meta($post->ID,'_wp_page_template',true);

  $hq_check = (has_term( 'hq', 'agency', $post->ID ) && $context != 'hq');
  $template_check = (!in_array($current_template, Agency_Editor::$restricted_templates) || current_user_can('administrator'));
  
  if (current_user_can('edit_posts') && $post->post_status == 'publish' && $hq_check &&  $template_check) {
    $actions['duplicate'] = '<a href="admin.php?action=dw_fork_post_as_draft&amp;post=' . $post->ID . '" title="Fork this item" rel="permalink">Fork</a>';
  }
  return $actions;
}

add_filter('post_row_actions', 'dw_fork_post_link', 10, 2);
add_filter('page_row_actions', 'dw_fork_post_link', 10, 2);