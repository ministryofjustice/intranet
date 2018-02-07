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

    $discussion_meta_box_value = (isset($post_meta['discussion_meta_box_value'][0]) && '' !== $post_meta['discussion_meta_box_value'][0]) ? $post_meta['discussion_meta_box_value'][0] : 'comments_off'; // default setting for new posts is comments off.

    // Messages we want to display to the editor about the managing comment options
    $meta_box_comments_on = 'Comments on - Comments are displayed fully on page.';
    $meta_box_comments_off = 'Comments off - Comments are completely removed from page.';
    $meta_box_comments_closed = 'Comments closed - Comments are displayed on page but further commments cannot be added.';

    // Create a nonce to check later.
    wp_nonce_field('comment_discussion_meta_box', 'discussion_meta_box_nonce');
    ?>

  	<div class="discussion_meta_box">
  		<p>
      <h2><strong>Manage the display of comments on the page. Choose an option and update the page.</strong></h2>
      <br>
  			<label>
  				<input type="radio" name="discussion_meta_box_value" value="comments_on" <?php checked($discussion_meta_box_value, 'comments_on'); ?>>
  				<?php esc_attr_e($meta_box_comments_on, 'clarity-theme'); ?>
  			</label>
  			<label>
  				<input type="radio" name="discussion_meta_box_value" value="comments_off" <?php checked($discussion_meta_box_value, 'comments_off'); ?>>
  				<?php esc_attr_e($meta_box_comments_off, 'clarity-theme'); ?>
  			</label>
  			<label>
  				<input type="radio" name="discussion_meta_box_value" value="comments_closed" <?php checked($discussion_meta_box_value, 'comments_closed'); ?>>
  				<?php esc_attr_e($meta_box_comments_closed, 'clarity-theme'); ?>
  			</label>
  		</p>
    </div>
  	<?php

}

/**
 * Save discussion meta box values.
 *
 * @param  int $post_id Current post id.
 *
 */
add_action('save_post', 'discussion_save_metaboxes');

function discussion_save_metaboxes($post_id)
{
    /*
     * Check and verify nonce.
     */
    if (!isset($_POST['discussion_meta_box_nonce']) || !wp_verify_nonce(sanitize_key($_POST['discussion_meta_box_nonce']), 'comment_discussion_meta_box')) {
        return $post_id;
    }

    // Stops a non-permitted user changing the comment setting.
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // Wordpress autosaves. We don't want it to autosave and change the discussion meta box value (possibly without a user knowing).
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Now it has been checked, save discussion meta box value and update the database with it.
    if (isset($_POST['discussion_meta_box_value'])) {
        update_post_meta($post_id, 'discussion_meta_box_value', sanitize_text_field(wp_unslash($_POST['discussion_meta_box_value'])));
    }
}
