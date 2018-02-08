<?php

/**
 * Classes and their methods responsible for managing comments in WP admin
 *
 */
// Exit if accessed directly
if (! defined('ABSPATH')) {
    die();
}

/**
 * WP does not enable you to modify meta boxes, rather you need to remove
 * the default discussion meta box and replace it with a custom meta box with the new title or features you require.
 *
 */
add_action('add_meta_boxes_post', 'update_discussion_meta_box');
add_action('add_meta_boxes_news', 'update_discussion_meta_box');
add_action('add_meta_boxes_page', 'update_discussion_meta_box');

function update_discussion_meta_box()
{
    // Applies to all post types being used on the site
    global $post_type;

    remove_meta_box('commentstatusdiv', $post_type, 'normal');
    add_meta_box('commentstatusdiv', __('Discussion'), 'custom_discussion_meta_box', $post_type, 'normal', 'low');
}

/**
 * Meta box render function
 *
 * @param  object $post Post object.
 * @since  1.0.0
 */
function custom_discussion_meta_box()
{
    $post_id = get_the_ID();
    $post_meta = get_post_meta(get_the_ID());

    $post = get_post();
    $post_comment_status = $post->comment_status;

    $comment_disabled_status = (isset($post_meta['comment_disabled_status'][0]) && '' !== $post_meta['comment_disabled_status'][0]) ? $post_meta['comment_disabled_status'][0] : '';

    // Messages we want to display to the editor about the managing comment options
    $comments_open_text = 'Comments on: comments are displayed on the page.';
    $comments_closed_text = 'Comments off: comments are not displayed on the page.';
    $comments_disabled_text = 'Comments closed: close current comments so that users cannot leave a new comment.';

    // Create a nonce to check later.
    wp_nonce_field('post_comment_status_check', 'post_comment_status_nonce');
    ?>

  	<div class="discussion_meta_box">
  		<p>
      <h2><strong>Manage the display of comments on the page. Choose an option and update the page.</strong></h2>
      <br>
  			<label>
  				<input type="radio" name="post_comment_status" value="open" <?php checked($post_comment_status, 'open'); ?>>
  				<?php esc_attr_e($comments_open_text, 'clarity'); ?>
  			</label>
        <label>
        <input type="checkbox" name="comment_disabled_status" value="comments_disabled" <?php checked($comment_disabled_status, 'comments_disabled'); ?> /><?php esc_attr_e($comments_disabled_text, 'clarity'); ?>
        </label>
          <br>
          <hr>
          <br>
  			<label>
  				<input type="radio" name="post_comment_status" value="closed" <?php checked($post_comment_status, 'closed'); ?>>
  				<?php esc_attr_e($comments_closed_text, 'clarity'); ?>
  			</label>
  		</p>
    </div>
  	<?php
}

/**
 * Where we save whatever value has been clicked above and update the database accordingly.
 *
 * @param int $post_id Current post id.
 *
 */
add_action('save_post', 'discussion_save_metaboxes');

function discussion_save_metaboxes($post_id)
{

    /*
     * Check and verify nonce.
     */
    if (!isset($_POST['post_comment_status_nonce']) || !wp_verify_nonce(sanitize_key($_POST['post_comment_status_nonce']), 'post_comment_status_check')) {
        return $post_id;
    }

    // Stops a non-permitted user changing the comment setting.
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // Wordpress autosaves. We don't want it to autosave and change the meta box values (possibly without a user knowing).
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Get the current comment status from the third comment disabled value
    $comments_disabled_status = (isset($_POST['comment_disabled_status']) && 'comments_disabled' === $_POST['comment_disabled_status']) ? 'comments_disabled' : 0;

    // Update the post data that handles comming disabled.
    update_post_meta($post_id, 'comment_disabled_status', esc_attr($comments_disabled_status));

    // To avoid an infinite loop using wp_update_post in a save function, we need to first remove and then re-add wp_update_post.
    remove_action('save_post', 'discussion_save_metaboxes');

    // Now it has been checked, save discussion meta box value and update the database with it.
    if (isset($_POST['post_comment_status'])) {
        $post_comment_status = $_POST['post_comment_status'];
      // Update the wp post 'comment_status' meta field.
      wp_update_post(['ID' => $post_id, 'comment_status' => sanitize_text_field(wp_unslash($post_comment_status))]);
    }

    add_action('save_post', 'discussion_save_metaboxes');
}
